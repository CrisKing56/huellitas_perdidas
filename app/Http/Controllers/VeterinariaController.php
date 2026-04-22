<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VeterinariaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $costo = trim((string) $request->get('costo', ''));
        $orden = trim((string) $request->get('orden', 'recientes'));

        $latitud = $request->filled('latitud') && is_numeric($request->latitud)
            ? (float) $request->latitud
            : null;

        $longitud = $request->filled('longitud') && is_numeric($request->longitud)
            ? (float) $request->longitud
            : null;

        $tieneTablaUbicaciones = Schema::hasTable('ubicaciones');
        $tieneTablaCostos = Schema::hasTable('organizacion_costo_servicio');

        $query = DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('organizacion_fotos as f', function ($join) {
                $join->on('f.organizacion_id', '=', 'o.id_organizacion')
                    ->where('f.orden', '=', 1);
            })
            ->where('o.tipo', 'VETERINARIA')
            ->where('o.estado_revision', 'APROBADA');

        if ($tieneTablaUbicaciones) {
            $query->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id');
        }

        if ($tieneTablaCostos) {
            $subCostos = DB::table('organizacion_costo_servicio')
                ->select(
                    'organizacion_id',
                    DB::raw('MIN(precio) as costo_minimo'),
                    DB::raw('MAX(precio) as costo_maximo')
                )
                ->groupBy('organizacion_id');

            $query->leftJoinSub($subCostos, 'costos', function ($join) {
                $join->on('costos.organizacion_id', '=', 'o.id_organizacion');
            });
        }

        $query->select(
            'o.id_organizacion',
            'o.nombre',
            'o.descripcion',
            'o.telefono',
            'd.calle_numero',
            'd.colonia',
            'd.ciudad',
            'd.estado as estado_direccion',
            'f.url as imagen'
        );

        if ($tieneTablaUbicaciones) {
            $query->addSelect(
                'ub.latitud',
                'ub.longitud'
            );
        } else {
            $query->selectRaw('NULL as latitud, NULL as longitud');
        }

        if ($tieneTablaCostos) {
            $query->addSelect(
                'costos.costo_minimo',
                'costos.costo_maximo'
            );
        } else {
            $query->selectRaw('NULL as costo_minimo, NULL as costo_maximo');
        }

        if ($q !== '') {
            $query->where(function ($subquery) use ($q) {
                $subquery->where('o.nombre', 'like', "%{$q}%")
                    ->orWhere('o.descripcion', 'like', "%{$q}%")
                    ->orWhere('o.telefono', 'like', "%{$q}%")
                    ->orWhere('d.calle_numero', 'like', "%{$q}%")
                    ->orWhere('d.colonia', 'like', "%{$q}%")
                    ->orWhere('d.ciudad', 'like', "%{$q}%")
                    ->orWhere('d.estado', 'like', "%{$q}%");
            });
        }

        if ($tieneTablaCostos) {
            switch ($costo) {
                case 'economico':
                    $query->whereNotNull('costos.costo_minimo')
                        ->where('costos.costo_minimo', '<=', 300);
                    break;

                case 'medio':
                    $query->whereNotNull('costos.costo_minimo')
                        ->whereBetween('costos.costo_minimo', [301, 700]);
                    break;

                case 'alto':
                    $query->whereNotNull('costos.costo_minimo')
                        ->where('costos.costo_minimo', '>=', 701);
                    break;

                case 'con_costos':
                    $query->whereNotNull('costos.costo_minimo');
                    break;
            }
        }

        $puedeOrdenarPorCercania = $tieneTablaUbicaciones && $latitud !== null && $longitud !== null;

        if ($puedeOrdenarPorCercania) {
            $query->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(ub.latitud)) * cos(radians(ub.longitud) - radians(?)) + sin(radians(?)) * sin(radians(ub.latitud)))) as distancia_km',
                [$latitud, $longitud, $latitud]
            );
        } else {
            $query->selectRaw('NULL as distancia_km');
        }

        switch ($orden) {
            case 'nombre':
                $query->orderBy('o.nombre');
                break;

            case 'costo_menor':
                if ($tieneTablaCostos) {
                    $query->orderByRaw('costos.costo_minimo IS NULL, costos.costo_minimo ASC');
                } else {
                    $query->orderByDesc('o.id_organizacion');
                }
                break;

            case 'costo_mayor':
                if ($tieneTablaCostos) {
                    $query->orderByRaw('costos.costo_minimo IS NULL, costos.costo_minimo DESC');
                } else {
                    $query->orderByDesc('o.id_organizacion');
                }
                break;

            case 'cercanas':
                if ($puedeOrdenarPorCercania) {
                    $query->orderBy('distancia_km');
                } else {
                    $query->orderByDesc('o.id_organizacion');
                }
                break;

            case 'recientes':
            default:
                $query->orderByDesc('o.id_organizacion');
                break;
        }

        $veterinarias = $query->paginate(9)->withQueryString();

        $filtros = [
            'q' => $q,
            'costo' => $costo,
            'orden' => $orden,
            'latitud' => $latitud,
            'longitud' => $longitud,
        ];

        return view('veterinarias.index', compact(
            'veterinarias',
            'filtros',
            'puedeOrdenarPorCercania',
            'tieneTablaCostos'
        ));
    }

    public function show($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
            ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.tipo', 'VETERINARIA')
            ->where('o.estado_revision', 'APROBADA')
            ->where('o.id_organizacion', $id)
            ->select(
                'o.*',
                'u.correo',
                'u.whatsapp',
                'd.calle_numero',
                'd.colonia',
                'd.codigo_postal',
                'd.ciudad',
                'd.estado as estado_direccion',
                'ub.latitud',
                'ub.longitud',
                'vd.medico_responsable',
                'vd.cedula_profesional',
                'vd.num_veterinarios',
                'vd.otros_servicios'
            )
            ->first();

        abort_if(!$veterinaria, 404);

        $horarios = DB::table('horarios_atencion')
            ->where('organizacion_id', $id)
            ->orderBy('dia_semana')
            ->get();

        $servicios = DB::table('organizacion_servicio as os')
            ->join('servicios as s', 's.id_servicio', '=', 'os.servicio_id')
            ->where('os.organizacion_id', $id)
            ->pluck('s.nombre');

        $costos = DB::table('organizacion_costo_servicio as ocs')
            ->join('servicios as s', 's.id_servicio', '=', 'ocs.servicio_id')
            ->where('ocs.organizacion_id', $id)
            ->select('s.nombre', 'ocs.precio', 'ocs.moneda')
            ->get();

        $fotos = DB::table('organizacion_fotos')
            ->where('organizacion_id', $id)
            ->orderBy('orden')
            ->get();

        return view('veterinarias.show', compact('veterinaria', 'horarios', 'servicios', 'costos', 'fotos'));
    }

    public function dashboard()
    {
        $usuario = Auth::user();

        abort_if(!$usuario, 403, 'No tienes una veterinaria asociada.');

        $organizacion = DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.usuario_dueno_id', $usuario->id_usuario)
            ->where('o.tipo', 'VETERINARIA')
            ->where('o.estado_revision', 'APROBADA')
            ->select(
                'o.*',
                'd.calle_numero',
                'd.colonia',
                'd.codigo_postal',
                'd.ciudad',
                'd.estado as estado_direccion',
                'vd.medico_responsable',
                'vd.cedula_profesional',
                'vd.num_veterinarios',
                'vd.otros_servicios'
            )
            ->first();

        abort_if(!$organizacion, 403, 'No tienes una veterinaria asociada.');

        $stats = [
            'consejos' => Schema::hasTable('consejos')
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'reportes_extravio' => Schema::hasTable('publicaciones_extravio')
                ? DB::table('publicaciones_extravio')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'servicios' => Schema::hasTable('organizacion_servicio')
                ? DB::table('organizacion_servicio')
                    ->where('organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'pendientes' => Schema::hasTable('consejos')
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->where('estado_publicacion', 'PENDIENTE')
                    ->count()
                : 0,
        ];

        $fotos = DB::table('organizacion_fotos')
            ->where('organizacion_id', $organizacion->id_organizacion)
            ->orderBy('orden')
            ->get();

        return view('veterinarias.dashboard', compact('organizacion', 'stats', 'fotos'));
    }
}