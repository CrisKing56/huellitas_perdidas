<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $usuarios = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('correo', 'like', "%{$q}%")
                      ->orWhere('id_usuario', $q);
            })
            ->orderByDesc('id_usuario')
            ->paginate(10)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:120|unique:usuarios,correo',
            'telefono' => 'required|digits:10',
            'rol' => 'required|in:USUARIO,ADMIN,VETERINARIA,REFUGIO',
            'estado' => 'required|in:ACTIVA,SUSPENDIDA,ELIMINADA',
            'password' => 'required|min:8',
        ]);

        User::create([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'],
            'rol' => $data['rol'],
            'estado' => $data['estado'],
            'password_hash' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();

        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:120|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'telefono' => 'required|digits:10',
            'rol' => 'required|in:USUARIO,ADMIN,VETERINARIA,REFUGIO',
            'estado' => 'required|in:ACTIVA,SUSPENDIDA,ELIMINADA',
            'password' => 'nullable|min:8',
        ]);

        $usuario->nombre = $data['nombre'];
        $usuario->correo = $data['correo'];
        $usuario->telefono = $data['telefono'];
        $usuario->rol = $data['rol'];
        $usuario->estado = $data['estado'];

        if (!empty($data['password'])) {
            $usuario->password_hash = Hash::make($data['password']);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();


        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
