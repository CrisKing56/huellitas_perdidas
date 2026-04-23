<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $rol = trim((string) $request->get('rol', ''));
        $estado = trim((string) $request->get('estado', ''));

        $usuarios = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subquery) use ($q) {
                    $subquery->where('nombre', 'like', "%{$q}%")
                        ->orWhere('correo', 'like', "%{$q}%")
                        ->orWhere('id_usuario', $q);
                });
            })
            ->when($rol !== '', function ($query) use ($rol) {
                $query->where('rol', $rol);
            })
            ->when($estado !== '', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->orderByDesc('id_usuario')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'total' => User::count(),
            'activas' => User::where('estado', 'ACTIVA')->count(),
            'suspendidas' => User::where('estado', 'SUSPENDIDA')->count(),
            'eliminadas' => User::where('estado', 'ELIMINADA')->count(),
            'admins' => User::where('rol', 'ADMIN')->count(),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'q', 'rol', 'estado', 'resumen'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'correo' => ['required', 'email', 'max:120', 'unique:usuarios,correo'],
            'telefono' => ['required', 'regex:/^[0-9]{10}$/'],
            'rol' => ['required', Rule::in(['USUARIO', 'ADMIN', 'VETERINARIA', 'REFUGIO'])],
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 120 caracteres.',

            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.max' => 'El correo no debe exceder 120 caracteres.',
            'correo.unique' => 'Ese correo ya está registrado.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener exactamente 10 números.',

            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',

            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = new User();
        $usuario->nombre = trim($data['nombre']);
        $usuario->correo = trim($data['correo']);
        $usuario->telefono = trim($data['telefono']);
        $usuario->rol = $data['rol'];
        $usuario->estado = 'ACTIVA';
        $usuario->auth_provider = 'LOCAL';
        $usuario->password_hash = Hash::make($data['password']);
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
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
            'nombre' => ['required', 'string', 'max:120'],
            'correo' => [
                'required',
                'email',
                'max:120',
                Rule::unique('usuarios', 'correo')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'telefono' => ['required', 'regex:/^[0-9]{10}$/'],
            'rol' => ['required', Rule::in(['USUARIO', 'ADMIN', 'VETERINARIA', 'REFUGIO'])],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 120 caracteres.',

            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.max' => 'El correo no debe exceder 120 caracteres.',
            'correo.unique' => 'Ese correo ya está registrado.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener exactamente 10 números.',

            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        if (Auth::check() && (int) Auth::user()->id_usuario === (int) $usuario->id_usuario && $data['rol'] !== 'ADMIN') {
            return back()
                ->withInput()
                ->with('error', 'No puedes quitarte a ti mismo el rol de administrador.');
        }

        $usuario->nombre = trim($data['nombre']);
        $usuario->correo = trim($data['correo']);
        $usuario->telefono = trim($data['telefono']);
        $usuario->rol = $data['rol'];
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function activate($id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();

        $usuario->estado = 'ACTIVA';
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'La cuenta fue activada correctamente.');
    }

    public function suspend($id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();

        if (Auth::check() && (int) Auth::user()->id_usuario === (int) $usuario->id_usuario) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes suspender tu propia cuenta.');
        }

        $usuario->estado = 'SUSPENDIDA';
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'La cuenta fue suspendida correctamente.');
    }

    public function destroy($id_usuario)
    {
        $usuario = User::where('id_usuario', $id_usuario)->firstOrFail();

        if (Auth::check() && (int) Auth::user()->id_usuario === (int) $usuario->id_usuario) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->estado = 'ELIMINADA';
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'La cuenta fue marcada como eliminada correctamente.');
    }
}