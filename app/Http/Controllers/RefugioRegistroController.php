<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RefugioRegistroController extends Controller
{
    public function create()
    {
        return view('refugios.registro-refugio');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_refugio'           => 'required|string|max:150',
            'descripcion'              => 'required|string',
            'correo'                   => 'required|email|max:120|unique:usuarios,correo',
            'password'                 => 'required|string|min:8|confirmed',
            'telefono'                 => 'required|digits:10',
            'whatsapp'                 => 'nullable|digits:10',

            'calle_numero'             => 'required|string|max:150',
            'colonia'                  => 'required|string|max:100',
            'codigo_postal'            => 'required|string|max:10',
            'ciudad'                   => 'required|string|max:100',
            'estado_direccion'         => 'required|string|max:100',

            'latitud'                  => 'required|numeric',
            'longitud'                 => 'required|numeric',

            'capacidad_perros'         => 'required|integer|min:0',
            'capacidad_gatos'          => 'required|integer|min:0',
            'instalaciones_descripcion'=> 'nullable|string',
            'requisitos_adopcion'      => 'required|string',
            'acepta_donaciones'        => 'required|boolean',
            'tipo_donaciones'          => 'nullable|string|max:255',

            'fotos'                    => 'nullable|array|max:10',
            'fotos.*'                  => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $usuarioId = DB::table('usuarios')->insertGetId([
                'correo'        => $request->correo,
                'password_hash' => Hash::make($request->password),
                'rol'           => 'REFUGIO',
                'nombre'        => $request->nombre_refugio,
                'telefono'      => $request->telefono,
                'whatsapp'      => $request->whatsapp,
                'estado'        => 'ACTIVA',
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

            return redirect()->route('registro.refugio')
                ->with('success', 'Solicitud de refugio enviada correctamente. Espera nuestra aprobación.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['Error al registrar: ' . $e->getMessage()]);
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