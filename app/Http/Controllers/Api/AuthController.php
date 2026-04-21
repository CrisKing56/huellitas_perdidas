<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        $tipoAccesoMovil = in_array($usuario->rol, ['VETERINARIA', 'REFUGIO'])
            ? 'ORGANIZACION'
            : 'USUARIO';

        return response()->json([
            'ok' => true,
            'message' => 'Login correcto',
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
                'estado' => $usuario->estado,
                'tipo_acceso_movil' => $tipoAccesoMovil,
            ]
        ], 200);
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
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
                'estado' => $usuario->estado,
                'tipo_acceso_movil' => 'USUARIO',
            ]
        ], 201);
    }
}