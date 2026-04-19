<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RefugioRegistroController extends Controller
{
    public function create()
    {
        return view('refugios.registro-refugio');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_refugio'            => 'required|string|max:150',
            'descripcion'               => 'required|string',
            'correo'                    => 'required|email|max:120|unique:usuarios,correo',
            'password'                  => 'required|string|min:8|confirmed',
            'telefono'                  => 'required|digits:10',
            'whatsapp'                  => 'nullable|digits:10',

            'calle_numero'              => 'required|string|max:150',
            'colonia'                   => 'required|string|max:100',
            'codigo_postal'             => 'required|string|max:10',
            'ciudad'                    => 'required|string|max:100',
            'estado_direccion'          => 'required|string|max:100',

            'latitud'                   => 'required|numeric',
            'longitud'                  => 'required|numeric',

            'capacidad_perros'          => 'required|integer|min:0',
            'capacidad_gatos'           => 'required|integer|min:0',
            'requisitos_adopcion'       => 'required|string',
            'acepta_donaciones'         => 'required|boolean',
            'tipo_donaciones'           => 'nullable|string',
            'instalaciones_descripcion' => 'nullable|string',

            'fotos'                     => 'nullable|array|max:10',
            'fotos.*'                   => 'image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'nombre_refugio.required' => 'El nombre del refugio es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Debes ingresar un correo válido.',
            'correo.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            'whatsapp.digits' => 'El WhatsApp debe tener exactamente 10 dígitos.',
            'calle_numero.required' => 'La calle y número son obligatorios.',
            'colonia.required' => 'La colonia es obligatoria.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'estado_direccion.required' => 'El estado es obligatorio.',
            'latitud.required' => 'Debes marcar una ubicación en el mapa.',
            'longitud.required' => 'Debes marcar una ubicación en el mapa.',
            'capacidad_perros.required' => 'La capacidad de perros es obligatoria.',
            'capacidad_gatos.required' => 'La capacidad de gatos es obligatoria.',
            'requisitos_adopcion.required' => 'Los requisitos de adopción son obligatorios.',
            'acepta_donaciones.required' => 'Debes indicar si aceptas donaciones.',
            'fotos.max' => 'Solo puedes subir hasta 10 imágenes.',
            'fotos.*.image' => 'Uno de los archivos no es una imagen válida.',
            'fotos.*.mimes' => 'Las imágenes deben ser JPG o PNG.',
            'fotos.*.max' => 'Cada imagen debe pesar como máximo 5 MB.',
        ]);

        DB::beginTransaction();

        try {
            $usuarioId = DB::table('usuarios')->insertGetId([
                'correo'        => $request->correo,
                'password_hash' => Hash::make($request->password),
                'rol'           => 'REFUGIO',
                'nombre'        => $request->nombre_refugio,
                'telefono'      => $request->telefono,
                'whatsapp'      => $request->whatsapp ?? null,
                'estado'        => 'ACTIVA',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $direccionId = DB::table('direcciones')->insertGetId([
                'calle_numero'  => $request->calle_numero,
                'colonia'       => $request->colonia,
                'codigo_postal' => $request->codigo_postal,
                'ciudad'        => $request->ciudad,
                'estado'        => $request->estado_direccion,
            ]);

            $ubicacionId = DB::table('ubicaciones')->insertGetId([
                'latitud'  => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            $organizacionId = DB::table('organizaciones')->insertGetId([
                'tipo'             => 'REFUGIO',
                'usuario_dueno_id' => $usuarioId,
                'nombre'           => $request->nombre_refugio,
                'descripcion'      => $request->descripcion,
                'telefono'         => $request->telefono,
                'direccion_id'     => $direccionId,
                'ubicacion_id'     => $ubicacionId,
                'estado_revision'  => 'PENDIENTE',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('refugio_detalle')->insert([
                'organizacion_id'           => $organizacionId,
                'capacidad_perros'          => $request->capacidad_perros,
                'capacidad_gatos'           => $request->capacidad_gatos,
                'instalaciones_descripcion' => $request->instalaciones_descripcion ?? '',
                'requisitos_adopcion'       => $request->requisitos_adopcion,
                'acepta_donaciones'         => $request->acepta_donaciones,
                'tipo_donaciones'           => $request->tipo_donaciones,
            ]);

            $this->guardarFotos($request, $organizacionId);

            DB::commit();

            $user = User::find($usuarioId);
            if ($user && is_null($user->email_verified_at)) {
                event(new Registered($user));
            }

            return redirect()->route('registro.organizacion.enviado')
                ->with('correo_verificacion', $request->correo)
                ->with('tipo_registro', 'refugio');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'Error al registrar: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function guardarFotos(Request $request, int $organizacionId): void
    {
        if (!$request->hasFile('fotos')) {
            return;
        }

        $orden = 1;

        foreach ($request->file('fotos') as $foto) {
            if (!$foto || !$foto->isValid()) {
                continue;
            }

            $ruta = $foto->store('organizaciones', 'public');

            DB::table('organizacion_fotos')->insert([
                'organizacion_id' => $organizacionId,
                'url'             => $ruta,
                'orden'           => $orden,
            ]);

            $orden++;
        }
    }
}