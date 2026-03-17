<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminRefugioController extends Controller
{
    public function index()
    {
        $refugios = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->where('o.tipo', 'REFUGIO')
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

        $activos = $refugios->where('estado_revision', 'APROBADA');
        $pendientes = $refugios->where('estado_revision', 'PENDIENTE');
        $rechazados = $refugios->where('estado_revision', 'RECHAZADA');

        return view('admin.refugios.index', compact('refugios', 'activos', 'pendientes', 'rechazados'));
    }

    public function show($id)
    {
        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
            ->leftJoin('refugio_detalle as rd', 'rd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.tipo', 'REFUGIO')
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
                'rd.tipo_organizacion',
                'rd.anio_fundacion',
                'rd.capacidad_total',
                'rd.animales_actuales',
                'rd.animales_dados_adopcion',
                'rd.anios_operacion',
                'rd.nombre_responsable',
                'rd.cargo_responsable',
                'rd.num_voluntarios',
                'rd.otras_especies'
            )
            ->first();

        abort_if(!$refugio, 404);

        $especies = DB::table('refugio_especie as re')
            ->join('especies as e', 'e.id_especie', '=', 're.especie_id')
            ->where('re.organizacion_id', $id)
            ->pluck('e.nombre');

        return view('admin.refugios.show', compact('refugio', 'especies'));
    }

    public function aprobar($id)
    {
        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'REFUGIO')
            ->update([
                'estado_revision' => 'APROBADA',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.refugios.index')->with('success', 'Refugio aprobado correctamente.');
    }

    public function rechazar($id)
    {
        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'REFUGIO')
            ->update([
                'estado_revision' => 'RECHAZADA',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.refugios.index')->with('success', 'Refugio rechazado correctamente.');
    }
}