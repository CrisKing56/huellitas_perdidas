<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicacionAdopcion;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class MobileSolicitudAdopcionController extends Controller
{
    private function usuarioIdActual(Request $request): ?int
    {
        $id = (int) ($request->input('id_usuario')
            ?? $request->input('solicitante_usuario_id')
            ?? $request->query('id_usuario')
            ?? 0);

        return $id > 0 ? $id : null;
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
                'urlSolicitudes' => route('adopciones.solicitudes.recibidas', [
                    'publicacion' => $publicacion->id_publicacion
                ]),
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

    private function mapFotoPrincipal(?PublicacionAdopcion $publicacion): ?array
    {
        if (!$publicacion || !$publicacion->fotoPrincipal) {
            return null;
        }

        return [
            'id_foto' => $publicacion->fotoPrincipal->id_foto,
            'url' => $publicacion->fotoPrincipal->url,
            'url_completa' => asset('storage/' . ltrim($publicacion->fotoPrincipal->url, '/')),
        ];
    }

    private function mapPublicacionResumen(?PublicacionAdopcion $publicacion): ?array
    {
        if (!$publicacion) {
            return null;
        }

        $foto = $this->mapFotoPrincipal($publicacion);

        return [
            'id_publicacion' => (int) $publicacion->id_publicacion,
            'nombre' => $publicacion->nombre,
            'estado' => $publicacion->estado,
            'descripcion' => $publicacion->descripcion,
            'colonia_barrio' => $publicacion->colonia_barrio,
            'edad_anios' => $publicacion->edad_anios,
            'sexo' => $publicacion->sexo,
            'tamano' => $publicacion->tamano,
            'otra_raza' => $publicacion->otra_raza,
            'especie_nombre' => $publicacion->especie->nombre ?? null,
            'raza_nombre' => $publicacion->raza->nombre ?? null,
            'foto_principal' => $foto,
            'foto_principal_url' => $foto['url_completa'] ?? null,
            'created_at' => $publicacion->created_at,
        ];
    }

    private function mapSolicitud(SolicitudAdopcion $solicitud): array
    {
        return [
            'id_solicitud' => (int) $solicitud->id_solicitud,
            'publicacion_id' => (int) $solicitud->publicacion_id,
            'solicitante_usuario_id' => (int) $solicitud->solicitante_usuario_id,
            'nombre_completo' => $solicitud->nombre_completo,
            'edad' => $solicitud->edad,
            'estado_civil' => $solicitud->estado_civil,
            'tipo_vivienda' => $solicitud->tipo_vivienda,
            'tiene_patio' => (bool) $solicitud->tiene_patio,
            'todos_de_acuerdo' => (bool) $solicitud->todos_de_acuerdo,
            'motivo_adopcion' => $solicitud->motivo_adopcion,
            'estado' => $solicitud->estado,
            'created_at' => $solicitud->created_at,
            'publicacion' => $this->mapPublicacionResumen($solicitud->publicacion),
            'solicitante' => [
                'id_usuario' => $solicitud->solicitante->id_usuario ?? null,
                'nombre' => $solicitud->solicitante->nombre ?? null,
                'correo' => $solicitud->solicitante->correo ?? null,
                'telefono' => $solicitud->solicitante->telefono ?? null,
            ],
        ];
    }

    public function store(Request $request, $id)
    {
        $idUsuario = $this->usuarioIdActual($request);

        if (!$idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No se encontró el usuario solicitante',
            ], 422);
        }

        $mascota = PublicacionAdopcion::with(['autor', 'fotoPrincipal', 'especie', 'raza'])->find($id);

        if (!$mascota) {
            return response()->json([
                'ok' => false,
                'message' => 'La publicación de adopción no existe',
            ], 404);
        }

        if ((int) $mascota->autor_usuario_id === (int) $idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No puedes enviar una solicitud para tu propia publicación.',
            ], 422);
        }

        if ($mascota->estado !== 'DISPONIBLE') {
            return response()->json([
                'ok' => false,
                'message' => 'Esta mascota ya no está disponible para recibir solicitudes.',
            ], 422);
        }

        $solicitudExistente = SolicitudAdopcion::where('publicacion_id', $mascota->id_publicacion)
            ->where('solicitante_usuario_id', $idUsuario)
            ->whereIn('estado', ['ENVIADA', 'ACEPTADA'])
            ->first();

        if ($solicitudExistente) {
            return response()->json([
                'ok' => false,
                'message' => 'Ya tienes una solicitud activa para esta mascota.',
            ], 422);
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
            ]
        );

        $solicitud = SolicitudAdopcion::create([
            'publicacion_id' => $mascota->id_publicacion,
            'solicitante_usuario_id' => $idUsuario,
            'nombre_completo' => trim($request->nombre_completo),
            'edad' => $request->edad,
            'estado_civil' => $request->estado_civil,
            'tipo_vivienda' => $request->tipo_vivienda,
            'tiene_patio' => (int) $request->tiene_patio,
            'todos_de_acuerdo' => (int) $request->todos_de_acuerdo,
            'motivo_adopcion' => trim($request->motivo_adopcion),
            'estado' => 'ENVIADA',
        ]);

        $solicitud->load(['publicacion.fotoPrincipal', 'publicacion.especie', 'publicacion.raza', 'publicacion.autor', 'solicitante']);
        $this->enviarCorreoSolicitudRecibida($solicitud);

        return response()->json([
            'ok' => true,
            'message' => 'Tu solicitud de adopción fue enviada correctamente.',
            'data' => $this->mapSolicitud($solicitud),
        ]);
    }

    public function enviadas($idUsuario)
    {
        $solicitudes = SolicitudAdopcion::with([
                'publicacion.fotoPrincipal',
                'publicacion.especie',
                'publicacion.raza',
                'publicacion.autor',
                'solicitante',
            ])
            ->where('solicitante_usuario_id', (int) $idUsuario)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($solicitud) => $this->mapSolicitud($solicitud))
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $solicitudes,
        ]);
    }

    public function recibidas(Request $request, $idUsuario)
    {
        $query = SolicitudAdopcion::with([
                'publicacion.fotoPrincipal',
                'publicacion.especie',
                'publicacion.raza',
                'publicacion.autor',
                'solicitante',
            ])
            ->whereHas('publicacion', function ($q) use ($idUsuario) {
                $q->where('autor_usuario_id', (int) $idUsuario);
            });

        if ($request->filled('publicacion')) {
            $query->where('publicacion_id', (int) $request->publicacion);
        }

        $solicitudes = $query
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($solicitud) => $this->mapSolicitud($solicitud))
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $solicitudes,
        ]);
    }

    public function updateEstado(Request $request, $id)
    {
        $idUsuario = $this->usuarioIdActual($request);

        if (!$idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No se encontró el usuario responsable',
            ], 422);
        }

        $solicitud = SolicitudAdopcion::with([
            'publicacion.fotoPrincipal',
            'publicacion.especie',
            'publicacion.raza',
            'publicacion.autor',
            'solicitante'
        ])->find($id);

        if (!$solicitud || !$solicitud->publicacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Solicitud no encontrada',
            ], 404);
        }

        if ((int) ($solicitud->publicacion->autor_usuario_id ?? 0) !== (int) $idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para gestionar esta solicitud',
            ], 403);
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

            $otrasSolicitudes = SolicitudAdopcion::with([
                    'publicacion.autor',
                    'solicitante',
                ])
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

        $solicitud->refresh();
        $solicitud->load([
            'publicacion.fotoPrincipal',
            'publicacion.especie',
            'publicacion.raza',
            'publicacion.autor',
            'solicitante'
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'El estado de la solicitud fue actualizado correctamente.',
            'data' => $this->mapSolicitud($solicitud),
        ]);
    }

    public function marcarAdoptada(Request $request, $id)
    {
        $idUsuario = $this->usuarioIdActual($request);

        if (!$idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No se encontró el usuario responsable',
            ], 422);
        }

        $publicacion = PublicacionAdopcion::find($id);

        if (!$publicacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación no encontrada',
            ], 404);
        }

        if ((int) ($publicacion->autor_usuario_id ?? 0) !== (int) $idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para actualizar esta publicación',
            ], 403);
        }

        $publicacion->estado = 'ADOPTADA';
        $publicacion->save();

        return response()->json([
            'ok' => true,
            'message' => 'La mascota fue marcada como adoptada.',
        ]);
    }

    public function volverEnProceso(Request $request, $id)
    {
        $idUsuario = $this->usuarioIdActual($request);

        if (!$idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No se encontró el usuario responsable',
            ], 422);
        }

        $publicacion = PublicacionAdopcion::find($id);

        if (!$publicacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación no encontrada',
            ], 404);
        }

        if ((int) ($publicacion->autor_usuario_id ?? 0) !== (int) $idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para actualizar esta publicación',
            ], 403);
        }

        $publicacion->estado = 'EN_PROCESO';
        $publicacion->save();

        return response()->json([
            'ok' => true,
            'message' => 'La publicación volvió a estado En proceso.',
        ]);
    }
}