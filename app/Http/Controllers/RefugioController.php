<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RefugioController extends Controller
{
    // Muestra el catálogo de refugios
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $orden = trim((string) $request->get('orden', 'recientes'));

        $latitud = $request->filled('latitud') && is_numeric($request->latitud)
            ? (float) $request->latitud
            : null;

        $longitud = $request->filled('longitud') && is_numeric($request->longitud)
            ? (float) $request->longitud
            : null;

        $tieneTablaUbicaciones = Schema::hasTable('ubicaciones');

        $query = DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('organizacion_fotos as f', function ($join) {
                $join->on('f.organizacion_id', '=', 'o.id_organizacion')
                    ->where('f.orden', '=', 1);
            })
            ->where('o.tipo', 'REFUGIO')
            ->where('o.estado_revision', 'APROBADA');

        if ($tieneTablaUbicaciones) {
            $query->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id');
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

        $refugios = $query->paginate(9)->withQueryString();

        $filtros = [
            'q' => $q,
            'orden' => $orden,
            'latitud' => $latitud,
            'longitud' => $longitud,
        ];

        return view('refugios.index', compact(
            'refugios',
            'filtros',
            'puedeOrdenarPorCercania'
        ));
    }

    // Muestra el perfil individual de un refugio
    public function show($id)
    {
        $refugio = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->findOrFail($id);

        return view('refugios.show', compact('refugio'));
    }

    // Panel privado del refugio
    public function dashboard()
    {
        $usuario = Auth::user();

        abort_if(!$usuario, 403, 'No tienes un refugio asociado.');

        $organizacion = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->where('estado_revision', 'APROBADA')
            ->where('usuario_dueno_id', $usuario->id_usuario)
            ->first();

        abort_if(!$organizacion, 403, 'No tienes un refugio asociado.');

        $tieneTablaAdopciones = Schema::hasTable('publicaciones_adopcion');
        $tieneAutorOrgEnAdopciones = $tieneTablaAdopciones && Schema::hasColumn('publicaciones_adopcion', 'autor_organizacion_id');
        $tieneEstadoEnAdopciones = $tieneTablaAdopciones && Schema::hasColumn('publicaciones_adopcion', 'estado');

        $tieneTablaConsejos = Schema::hasTable('consejos');
        $tieneAutorOrgEnConsejos = $tieneTablaConsejos && Schema::hasColumn('consejos', 'autor_organizacion_id');
        $tieneEstadoPublicacionEnConsejos = $tieneTablaConsejos && Schema::hasColumn('consejos', 'estado_publicacion');

        $stats = [
            'adopciones' => ($tieneTablaAdopciones && $tieneAutorOrgEnAdopciones)
                ? DB::table('publicaciones_adopcion')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'consejos' => ($tieneTablaConsejos && $tieneAutorOrgEnConsejos)
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'mascotas_activas' => ($tieneTablaAdopciones && $tieneAutorOrgEnAdopciones && $tieneEstadoEnAdopciones)
                ? DB::table('publicaciones_adopcion')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->whereIn('estado', ['DISPONIBLE', 'EN_PROCESO', 'PAUSADA'])
                    ->count()
                : 0,

            'pendientes' => ($tieneTablaConsejos && $tieneAutorOrgEnConsejos && $tieneEstadoPublicacionEnConsejos)
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->where('estado_publicacion', 'PENDIENTE')
                    ->count()
                : 0,
        ];

        return view('refugios.dashboard', compact('organizacion', 'stats'));
    }
}