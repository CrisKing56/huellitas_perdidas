<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RefugioRegistroController extends Controller
{
    public function create()
    {
        return view('auth.registro-refugio');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_refugio'       => 'required|string|max:150',
            'descripcion'          => 'required|string',
            'correo'               => 'required|email|max:120|unique:usuarios,correo',
            'password'             => 'required|string|min:8|confirmed',
            'telefono'             => 'required|digits:10',
            
            'calle_numero'         => 'required|string|max:150',
            'colonia'              => 'required|string|max:100',
            'codigo_postal'        => 'required|string|max:10',
            'ciudad'               => 'required|string|max:100',
            'estado_direccion'     => 'required|string|max:100',
            
            'latitud'              => 'required|numeric',
            'longitud'             => 'required|numeric',
            
            'capacidad_perros'     => 'required|integer|min:0',
            'capacidad_gatos'      => 'required|integer|min:0',
            'requisitos_adopcion'  => 'required|string',
            'acepta_donaciones'    => 'required|boolean',
            'tipo_donaciones'      => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Crear Usuario (Rol: REFUGIO)
            $usuarioId = DB::table('usuarios')->insertGetId([
                'correo'        => $request->correo,
                'password_hash' => Hash::make($request->password),
                'rol'           => 'REFUGIO',
                'nombre'        => $request->nombre_refugio,
                'telefono'      => $request->telefono,
                'whatsapp'      => $request->whatsapp ?? null,
                'estado'        => 'ACTIVA',
            ]);

            // Crear Dirección
            $direccionId = DB::table('direcciones')->insertGetId([
                'calle_numero'  => $request->calle_numero,
                'colonia'       => $request->colonia,
                'codigo_postal' => $request->codigo_postal,
                'ciudad'        => $request->ciudad,
                'estado'        => $request->estado_direccion,
            ]);

            // Crear Ubicación
            $ubicacionId = DB::table('ubicaciones')->insertGetId([
                'latitud'    => $request->latitud,
                'longitud'   => $request->longitud,
            ]);

            // Crear Organización (Tipo: REFUGIO)
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

            // Crear el Detalle específico del Refugio
            DB::table('refugio_detalle')->insert([
                'organizacion_id'           => $organizacionId,
                'capacidad_perros'          => $request->capacidad_perros,
                'capacidad_gatos'           => $request->capacidad_gatos,
                'instalaciones_descripcion' => $request->instalaciones_descripcion ?? '',
                'requisitos_adopcion'       => $request->requisitos_adopcion,
                'acepta_donaciones'         => $request->acepta_donaciones,
                'tipo_donaciones'           => $request->tipo_donaciones,
            ]);
            
            DB::commit();

            return redirect()->route('registro.refugio')
                ->with('success', 'Solicitud de refugio enviada correctamente. Espera nuestra aprobación.');

        } catch (\Throwable $e) {
            DB::rollBack();
            // Esto te ayudará a ver exactamente dónde falla si hay un error
            return redirect()->back()->withErrors(['Error al registrar: ' . $e->getMessage()])->withInput();
        }
    }
}
