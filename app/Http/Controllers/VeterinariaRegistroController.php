<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class VeterinariaRegistroController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre_veterinaria'   => 'required|string|max:150',
            'descripcion'          => 'required|string',
            'correo'               => 'required|email|max:120|unique:usuarios,correo',
            'password'             => 'required|string|min:8|confirmed',
            'telefono'             => 'required|digits:10',
            'whatsapp'             => 'nullable|digits:10',
            'sitio_web'            => 'nullable|url|max:255',

            'calle_numero'         => 'required|string|max:150',
            'colonia'              => 'required|string|max:100',
            'codigo_postal'        => 'required|string|max:10',
            'ciudad'               => 'required|string|max:100',
            'estado_direccion'     => 'required|string|max:100',

            'latitud'              => 'required|numeric',
            'longitud'             => 'required|numeric',

            'medico_responsable'   => 'required|string|max:150',
            'cedula_profesional'   => 'required|string|max:50',
            'num_veterinarios'     => 'nullable|integer|min:1',
            'otros_servicios'      => 'nullable|string',

            'servicios'            => 'nullable|array',

            'horario_lv_apertura'  => 'nullable|date_format:H:i',
            'horario_lv_cierre'    => 'nullable|date_format:H:i',
            'horario_sab_apertura' => 'nullable|date_format:H:i',
            'horario_sab_cierre'   => 'nullable|date_format:H:i',
            'horario_dom_apertura' => 'nullable|date_format:H:i',
            'horario_dom_cierre'   => 'nullable|date_format:H:i',

            'costo_consulta'       => 'nullable|string|max:50',
            'costo_vacuna'         => 'nullable|string|max:50',
            'costo_esterilizacion' => 'nullable|string|max:50',
            'costo_cirugia'        => 'nullable|string|max:50',

            'fotos'                => 'nullable|array|max:10',
            'fotos.*'              => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $usuarioId = DB::table('usuarios')->insertGetId([
                'correo'        => $request->correo,
                'password_hash' => Hash::make($request->password),
                'rol'           => 'VETERINARIA',
                'nombre'        => $request->nombre_veterinaria,
                'telefono'      => $request->telefono,
                'whatsapp'      => $request->whatsapp,
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
                'tipo'             => 'VETERINARIA',
                'usuario_dueno_id' => $usuarioId,
                'nombre'           => $request->nombre_veterinaria,
                'descripcion'      => $request->descripcion,
                'telefono'         => $request->telefono,
                'direccion_id'     => $direccionId,
                'ubicacion_id'     => $ubicacionId,
                'estado_revision'  => 'PENDIENTE',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('veterinaria_detalle')->insert([
                'organizacion_id'    => $organizacionId,
                'medico_responsable' => $request->medico_responsable,
                'cedula_profesional' => $request->cedula_profesional,
                'num_veterinarios'   => $request->num_veterinarios,
                'otros_servicios'    => $request->otros_servicios,
            ]);

            $this->guardarHorarios($request, $organizacionId);
            $this->guardarServicios($request, $organizacionId);
            $this->guardarCostos($request, $organizacionId);
            $this->guardarFotos($request, $organizacionId);

            DB::commit();

            $user = User::find($usuarioId);
            if ($user && is_null($user->email_verified_at)) {
                event(new Registered($user));
            }

            return redirect()->route('registro.organizacion.enviado')
                ->with('correo_verificacion', $request->correo)
                ->with('tipo_registro', 'veterinaria');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo registrar la veterinaria: ' . $e->getMessage());
        }
    }

    private function guardarHorarios(Request $request, int $organizacionId): void
    {
        $horarios = [
            ['dia_semana' => 1, 'hora_apertura' => $request->horario_lv_apertura,  'hora_cierre' => $request->horario_lv_cierre],
            ['dia_semana' => 2, 'hora_apertura' => $request->horario_lv_apertura,  'hora_cierre' => $request->horario_lv_cierre],
            ['dia_semana' => 3, 'hora_apertura' => $request->horario_lv_apertura,  'hora_cierre' => $request->horario_lv_cierre],
            ['dia_semana' => 4, 'hora_apertura' => $request->horario_lv_apertura,  'hora_cierre' => $request->horario_lv_cierre],
            ['dia_semana' => 5, 'hora_apertura' => $request->horario_lv_apertura,  'hora_cierre' => $request->horario_lv_cierre],
            ['dia_semana' => 6, 'hora_apertura' => $request->horario_sab_apertura, 'hora_cierre' => $request->horario_sab_cierre],
            ['dia_semana' => 7, 'hora_apertura' => $request->horario_dom_apertura, 'hora_cierre' => $request->horario_dom_cierre],
        ];

        foreach ($horarios as $horario) {
            $cerrado = empty($horario['hora_apertura']) || empty($horario['hora_cierre']);

            DB::table('horarios_atencion')->insert([
                'organizacion_id' => $organizacionId,
                'dia_semana'      => $horario['dia_semana'],
                'hora_apertura'   => $cerrado ? null : $horario['hora_apertura'],
                'hora_cierre'     => $cerrado ? null : $horario['hora_cierre'],
                'cerrado'         => $cerrado ? 1 : 0,
            ]);
        }
    }

    private function guardarServicios(Request $request, int $organizacionId): void
    {
        $seleccionados = $request->input('servicios', []);

        if (empty($seleccionados)) {
            return;
        }

        $mapaServicios = [
            'Consulta general' => 'Consulta General',
            'Vacunación'       => 'Vacunación',
            'Cirugía'          => 'Cirugía General',
            'Esterilización'   => 'Esterilización',
        ];

        foreach ($seleccionados as $servicioTexto) {
            if (!isset($mapaServicios[$servicioTexto])) {
                continue;
            }

            $servicio = DB::table('servicios')
                ->where('nombre', $mapaServicios[$servicioTexto])
                ->first();

            if (!$servicio) {
                continue;
            }

            $yaExiste = DB::table('organizacion_servicio')
                ->where('organizacion_id', $organizacionId)
                ->where('servicio_id', $servicio->id_servicio)
                ->exists();

            if (!$yaExiste) {
                DB::table('organizacion_servicio')->insert([
                    'organizacion_id' => $organizacionId,
                    'servicio_id'     => $servicio->id_servicio,
                ]);
            }
        }
    }

    private function guardarCostos(Request $request, int $organizacionId): void
    {
        $mapaCostos = [
            'costo_consulta'       => 'Consulta General',
            'costo_vacuna'         => 'Vacunación',
            'costo_esterilizacion' => 'Esterilización',
            'costo_cirugia'        => 'Cirugía General',
        ];

        foreach ($mapaCostos as $campoFormulario => $nombreServicio) {
            $precio = $this->normalizarPrecio($request->input($campoFormulario));

            if ($precio === null) {
                continue;
            }

            $servicio = DB::table('servicios')
                ->where('nombre', $nombreServicio)
                ->first();

            if (!$servicio) {
                continue;
            }

            $yaExiste = DB::table('organizacion_costo_servicio')
                ->where('organizacion_id', $organizacionId)
                ->where('servicio_id', $servicio->id_servicio)
                ->exists();

            if (!$yaExiste) {
                DB::table('organizacion_costo_servicio')->insert([
                    'organizacion_id' => $organizacionId,
                    'servicio_id'     => $servicio->id_servicio,
                    'precio'          => $precio,
                ]);
            }
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

    private function normalizarPrecio($valor): ?float
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        $valor = str_replace(['$', ','], '', $valor);

        if (!is_numeric($valor)) {
            return null;
        }

        return (float) $valor;
    }
}