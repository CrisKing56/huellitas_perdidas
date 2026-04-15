<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VeterinariaController extends Controller
{
    public function index()
    {
        $veterinarias = DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('organizacion_fotos as f', function ($join) {
                $join->on('f.organizacion_id', '=', 'o.id_organizacion')
                    ->where('f.orden', '=', 1);
            })
            ->where('o.tipo', 'VETERINARIA')
            ->where('o.estado_revision', 'APROBADA')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.descripcion',
                'o.telefono',
                'd.calle_numero',
                'd.colonia',
                'd.ciudad',
                'd.estado as estado_direccion',
                'f.url as imagen'
            )
            ->orderByDesc('o.id_organizacion')
            ->get();

        return view('veterinarias.index', compact('veterinarias'));
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
        $organizacion = DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.usuario_dueno_id', Auth::id())
            ->where('o.tipo', 'VETERINARIA')
            ->select(
                'o.*',
                'd.colonia',
                'd.ciudad',
                'd.estado as estado_direccion',
                'vd.medico_responsable',
                'vd.cedula_profesional'
            )
            ->first();

        abort_if(!$organizacion, 403, 'No tienes una veterinaria asociada.');

        $stats = [
            'consejos' => Schema::hasTable('consejos')
                ? DB::table('consejos')->where('autor_organizacion_id', $organizacion->id_organizacion)->count()
                : 0,

            'reportes_extravio' => Schema::hasTable('publicaciones_extravio')
                ? DB::table('publicaciones_extravio')->where('autor_organizacion_id', $organizacion->id_organizacion)->count()
                : 0,

            'servicios' => Schema::hasTable('organizacion_servicio')
                ? DB::table('organizacion_servicio')->where('organizacion_id', $organizacion->id_organizacion)->count()
                : 0,

            'pendientes' => Schema::hasTable('consejos')
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->where('estado_publicacion', 'PENDIENTE')
                    ->count()
                : 0,
        ];

        return view('veterinarias.dashboard', compact('organizacion', 'stats'));
    }
}