<?php

namespace App\Http\Controllers;

use App\Models\PublicacionAdopcion;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SolicitudAdopcionController extends Controller
{
    private function usuarioIdActual()
    {
        return Auth::user()->id_usuario ?? null;
    }

    private function correoUsuario($usuario): ?string
    {
        if (!$usuario) {
            return null;
        }

        return $usuario->correo ?? $usuario->email ?? null;
    }

    private function enviarCorreoSolicitudRecibida(SolicitudAdopcion $solicitud): void
    {
        try {
            $solicitud->loadMissing(['publicacion.autor', 'solicitante']);

            $publicacion = $solicitud->publicacion;
            $dueno = $publicacion?->autor;
            $correoDestino = $this->correoUsuario($dueno);

            if (!$publicacion || !$dueno || !$correoDestino) {
                return;
            }

            $html = view('emails.adopcion-solicitud-recibida', [
                'duenoNombre' => $dueno->nombre ?? 'Usuario',
                'solicitanteNombre' => $solicitud->nombre_completo,
                'publicacion' => $publicacion,
                'urlSolicitudes' => route('adopciones.solicitudes.recibidas', ['publicacion' => $publicacion->id_publicacion]),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $publicacion) {
                $message->to($correoDestino)
                    ->subject('Nueva solicitud de adopción para ' . $publicacion->nombre);
            });
        } catch (\Throwable $e) {
        }
    }

    private function enviarCorreoSolicitudAceptada(SolicitudAdopcion $solicitud): void
    {
        try {
            $solicitud->loadMissing(['publicacion.autor', 'solicitante']);

            $publicacion = $solicitud->publicacion;
            $solicitante = $solicitud->solicitante;
            $dueno = $publicacion?->autor;
            $correoDestino = $this->correoUsuario($solicitante);

            if (!$publicacion || !$solicitante || !$correoDestino) {
                return;
            }

            $html = view('emails.adopcion-solicitud-aceptada', [
                'solicitanteNombre' => $solicitud->nombre_completo,
                'duenoNombre' => $dueno->nombre ?? 'Responsable',
                'publicacion' => $publicacion,
                'urlSolicitudes' => route('adopciones.solicitudes.enviadas'),
                'urlPublicacion' => route('adopciones.show', $publicacion->id_publicacion),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $publicacion) {
                $message->to($correoDestino)
                    ->subject('Tu solicitud para adoptar a ' . $publicacion->nombre . ' fue aceptada');
            });
        } catch (\Throwable $e) {
        }
    }

    private function enviarCorreoSolicitudRechazada(SolicitudAdopcion $solicitud): void
    {
        try {
            $solicitud->loadMissing(['publicacion.autor', 'solicitante']);

            $publicacion = $solicitud->publicacion;
            $solicitante = $solicitud->solicitante;
            $correoDestino = $this->correoUsuario($solicitante);

            if (!$publicacion || !$solicitante || !$correoDestino) {
                return;
            }

            $html = view('emails.adopcion-solicitud-rechazada', [
                'solicitanteNombre' => $solicitud->nombre_completo,
                'publicacion' => $publicacion,
                'urlSolicitudes' => route('adopciones.solicitudes.enviadas'),
                'urlPublicacion' => route('adopciones.show', $publicacion->id_publicacion),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $publicacion) {
                $message->to($correoDestino)
                    ->subject('Actualización de tu solicitud para ' . $publicacion->nombre);
            });
        } catch (\Throwable $e) {
        }
    }

    public function create($id)
    {
        $mascota = PublicacionAdopcion::with(['fotoPrincipal', 'especie', 'raza', 'autor'])->findOrFail($id);

        if ((int) $mascota->autor_usuario_id === (int) $this->usuarioIdActual()) {
            return redirect()
                ->route('adopciones.show', $mascota->id_publicacion)
                ->with('error', 'No puedes enviar una solicitud para tu propia publicación.');
        }

        if ($mascota->estado !== 'DISPONIBLE') {
            return redirect()
                ->route('adopciones.show', $mascota->id_publicacion)
                ->with('error', 'Esta mascota ya no está disponible para recibir solicitudes.');
        }

        $solicitudExistente = SolicitudAdopcion::where('publicacion_id', $mascota->id_publicacion)
            ->where('solicitante_usuario_id', $this->usuarioIdActual())
            ->whereIn('estado', ['ENVIADA', 'ACEPTADA'])
            ->first();

        if ($solicitudExistente) {
            return redirect()
                ->route('adopciones.solicitudes.enviadas')
                ->with('error', 'Ya tienes una solicitud activa para esta mascota.');
        }

        return view('adopciones.solicitud', compact('mascota'));
    }

    public function store(Request $request, $id)
    {
        $mascota = PublicacionAdopcion::with('autor')->findOrFail($id);

        if ((int) $mascota->autor_usuario_id === (int) $this->usuarioIdActual()) {
            return redirect()
                ->route('adopciones.show', $mascota->id_publicacion)
                ->with('error', 'No puedes enviar una solicitud para tu propia publicación.');
        }

        if ($mascota->estado !== 'DISPONIBLE') {
            return redirect()
                ->route('adopciones.show', $mascota->id_publicacion)
                ->with('error', 'Esta mascota ya no está disponible para recibir solicitudes.');
        }

        $solicitudExistente = SolicitudAdopcion::where('publicacion_id', $mascota->id_publicacion)
            ->where('solicitante_usuario_id', $this->usuarioIdActual())
            ->whereIn('estado', ['ENVIADA', 'ACEPTADA'])
            ->first();

        if ($solicitudExistente) {
            return redirect()
                ->route('adopciones.solicitudes.enviadas')
                ->with('error', 'Ya tienes una solicitud activa para esta mascota.');
        }

        $request->validate(
            [
                'nombre_completo' => ['required', 'string', 'max:150'],
                'edad' => ['required', 'integer', 'min:18', 'max:120'],
                'estado_civil' => ['required', Rule::in(['SOLTERO', 'CASADO', 'UNION_LIBRE'])],
                'tipo_vivienda' => ['required', Rule::in(['CASA', 'DEPARTAMENTO', 'CUARTO', 'OTRO'])],
                'tiene_patio' => ['required', 'boolean'],
                'todos_de_acuerdo' => ['required', 'boolean'],
                'motivo_adopcion' => ['required', 'string'],
            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'integer' => 'El campo :attribute debe ser un número entero.',
                'min.numeric' => 'El campo :attribute debe ser al menos :min.',
                'max.numeric' => 'El campo :attribute no puede ser mayor que :max.',
                'max.string' => 'El campo :attribute no puede tener más de :max caracteres.',
                'in' => 'Selecciona una opción válida para :attribute.',
                'boolean' => 'Selecciona una opción válida para :attribute.',
            ],
            [
                'nombre_completo' => 'nombre completo',
                'edad' => 'edad',
                'estado_civil' => 'estado civil',
                'tipo_vivienda' => 'tipo de vivienda',
                'tiene_patio' => 'si tienes patio',
                'todos_de_acuerdo' => 'si todos están de acuerdo',
                'motivo_adopcion' => 'motivo de adopción',
            ]
        );

        $solicitud = SolicitudAdopcion::create([
            'publicacion_id' => $mascota->id_publicacion,
            'solicitante_usuario_id' => $this->usuarioIdActual(),
            'nombre_completo' => trim($request->nombre_completo),
            'edad' => $request->edad,
            'estado_civil' => $request->estado_civil,
            'tipo_vivienda' => $request->tipo_vivienda,
            'tiene_patio' => (int) $request->tiene_patio,
            'todos_de_acuerdo' => (int) $request->todos_de_acuerdo,
            'motivo_adopcion' => trim($request->motivo_adopcion),
            'estado' => 'ENVIADA',
        ]);

        $solicitud->load(['publicacion.autor', 'solicitante']);
        $this->enviarCorreoSolicitudRecibida($solicitud);

        return redirect()
            ->route('adopciones.solicitudes.enviadas')
            ->with('success', 'Tu solicitud de adopción fue enviada correctamente.');
    }

    public function enviadas()
    {
        $solicitudes = SolicitudAdopcion::with([
                'publicacion.fotoPrincipal',
                'publicacion.especie',
                'publicacion.raza',
                'publicacion.autor',
                'solicitante',
            ])
            ->where('solicitante_usuario_id', $this->usuarioIdActual())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('adopciones.solicitudes-enviadas', compact('solicitudes'));
    }

    public function recibidas(Request $request)
    {
        $publicacionFiltro = null;

        if ($request->filled('publicacion')) {
            $publicacionFiltro = PublicacionAdopcion::where('id_publicacion', $request->publicacion)
                ->where('autor_usuario_id', $this->usuarioIdActual())
                ->firstOrFail();
        }

        $solicitudes = SolicitudAdopcion::with([
                'publicacion.fotoPrincipal',
                'publicacion.especie',
                'publicacion.raza',
                'publicacion.autor',
                'solicitante',
            ])
            ->whereHas('publicacion', function ($query) {
                $query->where('autor_usuario_id', $this->usuarioIdActual());
            })
            ->when($publicacionFiltro, function ($query) use ($publicacionFiltro) {
                $query->where('publicacion_id', $publicacionFiltro->id_publicacion);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('adopciones.solicitudes-recibidas', compact('solicitudes', 'publicacionFiltro'));
    }

    public function updateEstado(Request $request, $id)
    {
        $solicitud = SolicitudAdopcion::with(['publicacion.autor', 'solicitante'])->findOrFail($id);

        if ((int) ($solicitud->publicacion->autor_usuario_id ?? 0) !== (int) $this->usuarioIdActual()) {
            abort(403);
        }

        $request->validate([
            'estado' => ['required', Rule::in(['ACEPTADA', 'RECHAZADA'])],
        ]);

        $solicitud->estado = $request->estado;
        $solicitud->save();

        if ($request->estado === 'ACEPTADA' && $solicitud->publicacion) {
            if ($solicitud->publicacion->estado === 'DISPONIBLE') {
                $solicitud->publicacion->estado = 'EN_PROCESO';
                $solicitud->publicacion->save();
            }

            $otrasSolicitudes = SolicitudAdopcion::with(['publicacion.autor', 'solicitante'])
                ->where('publicacion_id', $solicitud->publicacion_id)
                ->where('id_solicitud', '!=', $solicitud->id_solicitud)
                ->where('estado', 'ENVIADA')
                ->get();

            foreach ($otrasSolicitudes as $otraSolicitud) {
                $otraSolicitud->estado = 'RECHAZADA';
                $otraSolicitud->save();
                $this->enviarCorreoSolicitudRechazada($otraSolicitud);
            }

            $this->enviarCorreoSolicitudAceptada($solicitud);
        }

        if ($request->estado === 'RECHAZADA') {
            $this->enviarCorreoSolicitudRechazada($solicitud);
        }

        return redirect()
            ->route('adopciones.solicitudes.recibidas', $request->filled('publicacion') ? ['publicacion' => $request->publicacion] : [])
            ->with('success', 'El estado de la solicitud fue actualizado correctamente.');
    }
}