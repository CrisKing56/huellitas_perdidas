<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminVeterinariaController extends Controller
{
    public function index()
    {
        $veterinarias = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->where('o.tipo', 'VETERINARIA')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.descripcion',
                'o.telefono',
                'o.estado_revision',
                'u.correo',
                'u.nombre as nombre_usuario',
                'd.calle_numero',
                'd.colonia',
                'd.ciudad',
                'd.estado as estado_direccion'
            )
            ->orderByDesc('o.id_organizacion')
            ->get();

        $activas = $veterinarias->where('estado_revision', 'APROBADA');
        $pendientes = $veterinarias->where('estado_revision', 'PENDIENTE');
        $rechazadas = $veterinarias->where('estado_revision', 'RECHAZADA');

        return view('admin.veterinarias.index', compact('veterinarias', 'activas', 'pendientes', 'rechazadas'));
    }

    public function show($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
            ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.tipo', 'VETERINARIA')
            ->where('o.id_organizacion', $id)
            ->select(
                'o.*',
                'u.nombre as nombre_usuario',
                'u.correo',
                'u.telefono as telefono_usuario',
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
            ->select('s.nombre', 'ocs.precio', 'ocs.moneda', 'ocs.nota')
            ->get();

        $fotos = DB::table('organizacion_fotos')
            ->where('organizacion_id', $id)
            ->orderBy('orden')
            ->get();

        return view('admin.veterinarias.show', compact(
            'veterinaria',
            'horarios',
            'servicios',
            'costos',
            'fotos'
        ));
    }

    public function aprobar($id)
    {
        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'VETERINARIA')
            ->update([
                'estado_revision' => 'APROBADA',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'Veterinaria aprobada correctamente.');
    }

    public function rechazar($id)
    {
        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'VETERINARIA')
            ->update([
                'estado_revision' => 'RECHAZADA',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'Veterinaria rechazada correctamente.');
    }
}