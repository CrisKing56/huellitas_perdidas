<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MobileOrganizacionController extends Controller
{
    public function index()
    {
        $organizaciones = DB::table('organizaciones')
            ->join('usuarios', 'organizaciones.usuario_dueno_id', '=', 'usuarios.id_usuario')
            ->leftJoin('direcciones', 'organizaciones.direccion_id', '=', 'direcciones.id_direccion')
            ->leftJoin('ubicaciones', 'organizaciones.ubicacion_id', '=', 'ubicaciones.id_ubicacion')
            ->select(
                'organizaciones.id_organizacion',
                'organizaciones.tipo',
                'organizaciones.nombre',
                'organizaciones.descripcion',
                'organizaciones.telefono',
                'organizaciones.whatsapp',
                'organizaciones.sitio_web',
                'organizaciones.estado_revision',
                'organizaciones.activo',
                'usuarios.nombre as nombre_dueno',
                'direcciones.calle_numero',
                'direcciones.colonia',
                'direcciones.codigo_postal',
                'direcciones.ciudad',
                'direcciones.estado as estado_direccion',
                'ubicaciones.latitud',
                'ubicaciones.longitud'
            )
            ->where('organizaciones.activo', 1)
            ->where('organizaciones.estado_revision', 'APROBADA')
            ->orderBy('organizaciones.nombre')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $organizaciones
        ]);
    }
}