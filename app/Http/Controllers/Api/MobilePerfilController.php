<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobilePerfilController extends Controller
{
    public function show($idUsuario)
    {
        $perfil = $this->armarPerfil((int) $idUsuario);

        if (!$perfil) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Perfil obtenido correctamente',
            'usuario' => $perfil,
        ]);
    }

    public function update(Request $request, $idUsuario)
    {
        $idUsuario = (int) $idUsuario;

        $usuario = DB::table('usuarios')
            ->where('id_usuario', $idUsuario)
            ->first();

        if (!$usuario) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'ciudad' => 'nullable|string|max:120',

            'organizacion_nombre' => 'nullable|string|max:180',
            'organizacion_telefono' => 'nullable|string|max:30',
            'organizacion_whatsapp' => 'nullable|string|max:30',
            'sitio_web' => 'nullable|string|max:255',
        ]);

        DB::table('usuarios')
            ->where('id_usuario', $idUsuario)
            ->update([
                'nombre' => trim((string) $request->input('nombre', $usuario->nombre)),
                'telefono' => $request->input('telefono'),
                'whatsapp' => $request->input('whatsapp'),
                'ciudad' => $request->input('ciudad'),
            ]);

        $rol = strtoupper((string) ($usuario->rol ?? ''));

        if (in_array($rol, ['VETERINARIA', 'REFUGIO'])) {
            $organizacion = DB::table('organizaciones')
                ->where('usuario_dueno_id', $idUsuario)
                ->first();

            if ($organizacion) {
                DB::table('organizaciones')
                    ->where('usuario_dueno_id', $idUsuario)
                    ->update([
                        'nombre' => $request->input('organizacion_nombre', $organizacion->nombre),
                        'telefono' => $request->input('organizacion_telefono', $organizacion->telefono),
                        'whatsapp' => $request->input('organizacion_whatsapp', $organizacion->whatsapp),
                        'sitio_web' => $request->input('sitio_web', $organizacion->sitio_web),
                    ]);
            }
        }

        $perfil = $this->armarPerfil($idUsuario);

        return response()->json([
            'ok' => true,
            'message' => 'Perfil actualizado correctamente',
            'usuario' => $perfil,
        ]);
    }

    public function settings(Request $request, $idUsuario)
    {
        $idUsuario = (int) $idUsuario;

        $usuario = DB::table('usuarios')
            ->where('id_usuario', $idUsuario)
            ->first();

        if (!$usuario) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        DB::table('usuario_configuracion')->updateOrInsert(
            ['usuario_id' => $idUsuario],
            [
                'mostrar_telefono_publico' => $this->boolToInt($request->input('mostrar_telefono_publico')),
                'mostrar_whatsapp_publico' => $this->boolToInt($request->input('mostrar_whatsapp_publico')),
                'ocultar_ubicacion_exacta' => $this->boolToInt($request->input('ocultar_ubicacion_exacta')),
                'recibir_notificaciones' => $this->boolToInt($request->input('recibir_notificaciones')),
                'recibir_correos' => $this->boolToInt($request->input('recibir_correos')),
                'actualizado_en' => now(),
            ]
        );

        $perfil = $this->armarPerfil($idUsuario);

        return response()->json([
            'ok' => true,
            'message' => 'Configuración actualizada correctamente',
            'usuario' => $perfil,
        ]);
    }

    private function armarPerfil(int $idUsuario): ?array
    {
        $usuario = DB::table('usuarios')
            ->where('id_usuario', $idUsuario)
            ->first();

        if (!$usuario) {
            return null;
        }

        $configuracion = DB::table('usuario_configuracion')
            ->where('usuario_id', $idUsuario)
            ->first();

        $rol = strtoupper((string) ($usuario->rol ?? ''));
        $esInstitucional = in_array($rol, ['VETERINARIA', 'REFUGIO']);

        $organizacion = null;

        if ($esInstitucional) {
            $org = DB::table('organizaciones')
                ->where('usuario_dueno_id', $idUsuario)
                ->select(
                    'id_organizacion',
                    'tipo',
                    'nombre',
                    'telefono',
                    'whatsapp',
                    'sitio_web',
                    'estado_revision'
                )
                ->first();

            if ($org) {
                $organizacion = [
                    'id_organizacion' => $org->id_organizacion,
                    'tipo' => $org->tipo,
                    'nombre' => $org->nombre,
                    'telefono' => $org->telefono,
                    'whatsapp' => $org->whatsapp,
                    'sitio_web' => $org->sitio_web,
                    'estado_revision' => $org->estado_revision,
                ];
            }
        }

        return [
            'id_usuario' => (int) $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'telefono' => $usuario->telefono,
            'whatsapp' => $usuario->whatsapp ?? null,
            'ciudad' => $usuario->ciudad ?? null,
            'rol' => $usuario->rol,
            'estado' => $usuario->estado,
            'tipo_acceso_movil' => $esInstitucional ? 'ORGANIZACION' : 'USUARIO',
            'configuracion' => [
                'mostrar_telefono_publico' => (bool) ($configuracion->mostrar_telefono_publico ?? false),
                'mostrar_whatsapp_publico' => (bool) ($configuracion->mostrar_whatsapp_publico ?? false),
                'ocultar_ubicacion_exacta' => (bool) ($configuracion->ocultar_ubicacion_exacta ?? false),
                'recibir_notificaciones' => (bool) ($configuracion->recibir_notificaciones ?? false),
                'recibir_correos' => (bool) ($configuracion->recibir_correos ?? false),
            ],
            'organizacion' => $organizacion,
        ];
    }

    private function boolToInt($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1 ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'si', 'sí', 'on']) ? 1 : 0;
    }
}