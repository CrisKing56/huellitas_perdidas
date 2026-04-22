<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicacionAdopcion;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileAdopcionController extends Controller
{
    public function detalle(Request $request, int $id): JsonResponse
    {
        try {
            $idUsuario = (int) $request->query('id_usuario', 0);

            $publicacion = PublicacionAdopcion::with([
                'autor',
                'fotoPrincipal',
                'fotos',
                'especie',
                'raza',
            ])->find($id);

            if (!$publicacion) {
                return response()->json([
                    'ok' => false,
                    'message' => 'La publicación no existe.',
                ], 404);
            }

            $esAutor = $idUsuario > 0
                && (int) $publicacion->autor_usuario_id === $idUsuario;

            $solicitudAceptada = null;
            $estadoSolicitud = '';

            if ($idUsuario > 0 && !$esAutor) {
                $solicitud = SolicitudAdopcion::where('publicacion_id', $publicacion->id_publicacion)
                    ->where('solicitante_usuario_id', $idUsuario)
                    ->latest()
                    ->first();

                if ($solicitud) {
                    $estadoSolicitud = (string) ($solicitud->estado ?? '');

                    if (strtoupper($estadoSolicitud) === 'ACEPTADA') {
                        $solicitudAceptada = $solicitud;
                    }
                }
            }

            $contactoVisible = $esAutor || $solicitudAceptada !== null;

            $estadoPublicacion = strtoupper((string) ($publicacion->estado ?? 'DISPONIBLE'));

            $puedeSolicitar = !$esAutor
                && $estadoPublicacion === 'DISPONIBLE'
                && strtoupper($estadoSolicitud) !== 'ACEPTADA'
                && strtoupper($estadoSolicitud) !== 'ENVIADA';

            $fotoPrincipalUrl = '';

            if ($publicacion->fotoPrincipal) {
                $fotoPrincipalUrl = $this->resolverUrlFoto($publicacion->fotoPrincipal);
            }

            $fotos = [];
            foreach ($publicacion->fotos as $foto) {
                $url = $this->resolverUrlFoto($foto);

                if ($url !== '') {
                    $fotos[] = [
                        'url_completa' => $url,
                    ];
                }
            }

            if ($fotoPrincipalUrl === '' && count($fotos) > 0) {
                $fotoPrincipalUrl = $fotos[0]['url_completa'];
            }

            return response()->json([
                'ok' => true,
                'data' => [
                    'id_publicacion' => $publicacion->id_publicacion,
                    'nombre' => $publicacion->nombre ?? 'Mascota',
                    'estado' => $publicacion->estado ?? 'DISPONIBLE',
                    'especie_nombre' => optional($publicacion->especie)->nombre ?? 'Mascota',
                    'raza_nombre' => optional($publicacion->raza)->nombre ?? '',
                    'otra_raza' => $publicacion->otra_raza ?? '',
                    'edad_anios' => $publicacion->edad_anios !== null ? (string) $publicacion->edad_anios : '',
                    'sexo' => $publicacion->sexo ?? '',
                    'tamano' => $publicacion->tamano ?? '',
                    'color_predominante' => $publicacion->color_predominante ?? '',
                    'descripcion' => $publicacion->descripcion ?? '',
                    'condicion_salud' => $publicacion->condicion_salud ?? '',
                    'vacunas_aplicadas' => $publicacion->vacunas_aplicadas ?? '',
                    'descripcion_salud' => $publicacion->descripcion_salud ?? '',
                    'requisitos' => $publicacion->requisitos ?? '',
                    'colonia_barrio' => $publicacion->colonia_barrio ?? '',
                    'calle_referencias' => $publicacion->calle_referencias ?? '',
                    'latitud' => $publicacion->latitud !== null ? (string) $publicacion->latitud : '',
                    'longitud' => $publicacion->longitud !== null ? (string) $publicacion->longitud : '',
                    'foto_principal_url' => $fotoPrincipalUrl,
                    'fotos' => $fotos,
                    'autor' => [
                        'nombre' => optional($publicacion->autor)->nombre ?? 'Responsable',
                        'telefono' => optional($publicacion->autor)->telefono ?? '',
                        'whatsapp' => optional($publicacion->autor)->whatsapp
                            ?? optional($publicacion->autor)->telefono
                            ?? '',
                        'correo' => optional($publicacion->autor)->correo ?? '',
                    ],
                    'es_autor' => $esAutor,
                    'contacto_visible' => $contactoVisible,
                    'puede_solicitar' => $puedeSolicitar,
                    'estado_solicitud' => $estadoSolicitud,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al cargar el detalle de adopción.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function marcarAdoptada(Request $request, int $id): JsonResponse
    {
        try {
            $idUsuario = (int) $request->input('id_usuario', 0);

            /** @var \App\Models\PublicacionAdopcion|null $publicacion */
            $publicacion = PublicacionAdopcion::find($id);

            if (!$publicacion) {
                return response()->json([
                    'ok' => false,
                    'message' => 'La publicación no existe.',
                ], 404);
            }

            if ((int) $publicacion->autor_usuario_id !== $idUsuario) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No tienes permiso para cambiar el estado de esta publicación.',
                ], 403);
            }

            if ($publicacion->estado !== 'EN_PROCESO') {
                return response()->json([
                    'ok' => false,
                    'message' => 'Solo puedes marcar como adoptada una publicación que esté en proceso.',
                ], 422);
            }

            $publicacion->estado = 'ADOPTADA';
            $publicacion->save();

            return response()->json([
                'ok' => true,
                'message' => 'La publicación fue marcada como adoptada.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo marcar como adoptada.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function volverEnProceso(Request $request, int $id): JsonResponse
    {
        try {
            $idUsuario = (int) $request->input('id_usuario', 0);

            /** @var \App\Models\PublicacionAdopcion|null $publicacion */
            $publicacion = PublicacionAdopcion::find($id);

            if (!$publicacion) {
                return response()->json([
                    'ok' => false,
                    'message' => 'La publicación no existe.',
                ], 404);
            }

            if ((int) $publicacion->autor_usuario_id !== $idUsuario) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No tienes permiso para cambiar el estado de esta publicación.',
                ], 403);
            }

            if ($publicacion->estado !== 'ADOPTADA') {
                return response()->json([
                    'ok' => false,
                    'message' => 'Solo puedes regresar a proceso una publicación que esté adoptada.',
                ], 422);
            }

            $publicacion->estado = 'EN_PROCESO';
            $publicacion->save();

            return response()->json([
                'ok' => true,
                'message' => 'La publicación volvió a EN_PROCESO.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo regresar la publicación a EN_PROCESO.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolverUrlFoto(object $foto): string
    {
        $candidatos = [
            $foto->url_completa ?? null,
            $foto->url ?? null,
            $foto->ruta_foto ?? null,
            $foto->foto ?? null,
            $foto->imagen ?? null,
            $foto->ruta ?? null,
            $foto->archivo ?? null,
        ];

        foreach ($candidatos as $valor) {
            if (!is_string($valor) || trim($valor) === '') {
                continue;
            }

            $ruta = trim($valor);

            if (Str::startsWith($ruta, ['http://', 'https://'])) {
                return $ruta;
            }

            if (Str::startsWith($ruta, ['storage/', 'uploads/', 'img/', 'images/'])) {
                return asset($ruta);
            }

            return asset('storage/' . ltrim($ruta, '/'));
        }

        return '';
    }
}