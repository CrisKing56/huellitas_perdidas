<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileOrganizacionController extends Controller
{
    public function index(Request $request)
    {
        $tipo = strtoupper($request->query('tipo', ''));

        $query = DB::table('organizaciones')
            ->join('usuarios', 'organizaciones.usuario_dueno_id', '=', 'usuarios.id_usuario')
            ->leftJoin('direcciones', 'organizaciones.direccion_id', '=', 'direcciones.id_direccion')
            ->leftJoin('ubicaciones', 'organizaciones.ubicacion_id', '=', 'ubicaciones.id_ubicacion')
            ->leftJoin('organizacion_fotos as foto', function ($join) {
                $join->on('foto.organizacion_id', '=', 'organizaciones.id_organizacion')
                    ->where('foto.orden', '=', 1);
            })
            ->select(
                'organizaciones.id_organizacion',
                'organizaciones.tipo',
                'organizaciones.nombre',
                'organizaciones.descripcion',
                'organizaciones.telefono',
                'organizaciones.estado_revision',
                'usuarios.nombre as nombre_dueno',
                'usuarios.correo',
                'usuarios.whatsapp',
                'direcciones.calle_numero',
                'direcciones.colonia',
                'direcciones.codigo_postal',
                'direcciones.ciudad',
                'direcciones.estado as estado_direccion',
                'ubicaciones.latitud',
                'ubicaciones.longitud',
                'foto.url as foto_principal'
            )
            ->where('organizaciones.estado_revision', 'APROBADA')
            ->whereIn('organizaciones.tipo', ['VETERINARIA', 'REFUGIO'])
            ->orderBy('organizaciones.nombre');

        if (in_array($tipo, ['VETERINARIA', 'REFUGIO'])) {
            $query->where('organizaciones.tipo', $tipo);
        }

        $organizaciones = $query->get()->map(function ($org) {
            $org->direccion_completa = collect([
                $org->calle_numero,
                $org->colonia,
                $org->ciudad,
                $org->estado_direccion,
            ])->filter()->implode(', ');

            $org->foto_principal_url = $org->foto_principal
                ? asset('storage/' . $org->foto_principal)
                : null;

            return $org;
        });

        return response()->json([
            'ok' => true,
            'data' => $organizaciones
        ]);
    }

    public function show($id)
    {
        $organizacion = DB::table('organizaciones')
            ->join('usuarios', 'organizaciones.usuario_dueno_id', '=', 'usuarios.id_usuario')
            ->leftJoin('direcciones', 'organizaciones.direccion_id', '=', 'direcciones.id_direccion')
            ->leftJoin('ubicaciones', 'organizaciones.ubicacion_id', '=', 'ubicaciones.id_ubicacion')
            ->select(
                'organizaciones.id_organizacion',
                'organizaciones.tipo',
                'organizaciones.nombre',
                'organizaciones.descripcion',
                'organizaciones.telefono',
                'organizaciones.estado_revision',
                'usuarios.nombre as nombre_dueno',
                'usuarios.correo',
                'usuarios.whatsapp',
                'direcciones.calle_numero',
                'direcciones.colonia',
                'direcciones.codigo_postal',
                'direcciones.ciudad',
                'direcciones.estado as estado_direccion',
                'ubicaciones.latitud',
                'ubicaciones.longitud'
            )
            ->where('organizaciones.id_organizacion', $id)
            ->whereIn('organizaciones.tipo', ['VETERINARIA', 'REFUGIO'])
            ->where('organizaciones.estado_revision', 'APROBADA')
            ->first();

        if (!$organizacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Organización no encontrada.'
            ], 404);
        }

        $fotos = DB::table('organizacion_fotos')
            ->where('organizacion_id', $id)
            ->orderBy('orden')
            ->get()
            ->map(function ($foto) {
                return [
                    'id_foto' => $foto->id_foto,
                    'orden' => $foto->orden,
                    'url' => $foto->url,
                    'url_completa' => asset('storage/' . $foto->url),
                ];
            })
            ->values();

        $horarios = DB::table('horarios_atencion')
            ->where('organizacion_id', $id)
            ->orderBy('dia_semana')
            ->get()
            ->map(function ($horario) {
                return [
                    'dia_semana' => $horario->dia_semana,
                    'hora_apertura' => $horario->hora_apertura,
                    'hora_cierre' => $horario->hora_cierre,
                    'cerrado' => (bool) $horario->cerrado,
                ];
            })
            ->values();

        $organizacion->direccion_completa = collect([
            $organizacion->calle_numero,
            $organizacion->colonia,
            $organizacion->ciudad,
            $organizacion->estado_direccion,
        ])->filter()->implode(', ');

        $organizacion->fotos = $fotos;
        $organizacion->foto_principal_url = $fotos->isNotEmpty() ? $fotos[0]['url_completa'] : null;
        $organizacion->horarios = $horarios;

        // Valores por defecto para no romper pantallas existentes
        $organizacion->medico_responsable = null;
        $organizacion->cedula_profesional = null;
        $organizacion->num_veterinarios = null;
        $organizacion->otros_servicios = null;
        $organizacion->servicios = [];
        $organizacion->costos = [];

        if ($organizacion->tipo === 'VETERINARIA') {
            $detalleVet = DB::table('veterinaria_detalle')
                ->where('organizacion_id', $id)
                ->first();

            if ($detalleVet) {
                $organizacion->medico_responsable = $detalleVet->medico_responsable;
                $organizacion->cedula_profesional = $detalleVet->cedula_profesional;
                $organizacion->num_veterinarios = $detalleVet->num_veterinarios;
                $organizacion->otros_servicios = $detalleVet->otros_servicios;
            }

            $servicios = DB::table('organizacion_servicio as os')
                ->join('servicios as s', 'os.servicio_id', '=', 's.id_servicio')
                ->where('os.organizacion_id', $id)
                ->select(
                    's.id_servicio',
                    's.nombre'
                )
                ->orderBy('s.nombre')
                ->get()
                ->values();

            $costos = DB::table('organizacion_costo_servicio as ocs')
                ->join('servicios as s', 'ocs.servicio_id', '=', 's.id_servicio')
                ->where('ocs.organizacion_id', $id)
                ->select(
                    's.nombre as servicio',
                    'ocs.precio',
                    'ocs.moneda',
                    'ocs.nota'
                )
                ->orderBy('s.nombre')
                ->get()
                ->values();

            $organizacion->servicios = $servicios;
            $organizacion->costos = $costos;
        }

        return response()->json([
            'ok' => true,
            'data' => $organizacion
        ]);
    }
}