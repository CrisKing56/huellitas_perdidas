<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = User::where('correo', $request->correo)->first();

        if (!$usuario) {
            return response()->json([
                'ok' => false,
                'message' => 'Correo no encontrado'
            ], 404);
        }

        if (!Hash::check($request->password, $usuario->password_hash)) {
            return response()->json([
                'ok' => false,
                'message' => 'Contraseña incorrecta'
            ], 401);
        }

        if (($usuario->estado ?? null) !== 'ACTIVA') {
            return response()->json([
                'ok' => false,
                'message' => 'La cuenta no está activa'
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Login correcto',
            'usuario' => $this->payloadUsuario($usuario),
        ], 200);
    }

    public function loginGoogle(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $clientId = env('GOOGLE_WEB_CLIENT_ID');
            $googleClient = $clientId
                ? new GoogleClient(['client_id' => $clientId])
                : new GoogleClient();

            $payload = $googleClient->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Token de Google inválido'
                ], 401);
            }

            $correo = $payload['email'] ?? null;
            $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $googleId = $payload['sub'] ?? null;
            $nombre = $payload['name'] ?? 'Usuario Google';
            $foto = $payload['picture'] ?? null;

            if (!$correo || !$emailVerified) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Google no devolvió un correo verificado'
                ], 422);
            }

            $usuario = User::where('correo', $correo)->first();

            if (!$usuario) {
                $data = [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'password_hash' => Hash::make(Str::random(40)),
                    'rol' => 'USUARIO',
                    'estado' => 'ACTIVA',
                ];

                if (Schema::hasColumn('usuarios', 'telefono')) {
                    $data['telefono'] = null;
                }

                if (Schema::hasColumn('usuarios', 'google_id')) {
                    $data['google_id'] = $googleId;
                }

                if (Schema::hasColumn('usuarios', 'foto_perfil')) {
                    $data['foto_perfil'] = $foto;
                }

                $usuario = User::create($data);
            } else {
                if (($usuario->estado ?? null) !== 'ACTIVA') {
                    return response()->json([
                        'ok' => false,
                        'message' => 'La cuenta no está activa'
                    ], 403);
                }

                $updates = [];

                if (empty($usuario->nombre) && !empty($nombre)) {
                    $updates['nombre'] = $nombre;
                }

                if (Schema::hasColumn('usuarios', 'google_id') && empty($usuario->google_id) && !empty($googleId)) {
                    $updates['google_id'] = $googleId;
                }

                if (Schema::hasColumn('usuarios', 'foto_perfil') && empty($usuario->foto_perfil) && !empty($foto)) {
                    $updates['foto_perfil'] = $foto;
                }

                if (!empty($updates)) {
                    $usuario->fill($updates);
                    $usuario->save();
                }
            }

            return response()->json([
                'ok' => true,
                'message' => 'Login con Google correcto',
                'usuario' => $this->payloadUsuario($usuario),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo iniciar sesión con Google',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:120|unique:usuarios,correo',
            'telefono' => 'required|digits:10|unique:usuarios,telefono',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password_hash' => Hash::make($request->password),
            'rol' => 'USUARIO',
            'estado' => 'ACTIVA',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Usuario registrado correctamente',
            'usuario' => $this->payloadUsuario($usuario),
        ], 201);
    }

    private function payloadUsuario($usuario): array
    {
        $tipoAccesoMovil = in_array($usuario->rol, ['VETERINARIA', 'REFUGIO'])
            ? 'ORGANIZACION'
            : 'USUARIO';

        return [
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'telefono' => $usuario->telefono,
            'rol' => $usuario->rol,
            'estado' => $usuario->estado,
            'tipo_acceso_movil' => $tipoAccesoMovil,
        ];
    }
}