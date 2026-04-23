<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdopcionFoto;
use App\Models\PublicacionAdopcion;
use App\Models\Raza;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'autor_usuario_id' => ['required', 'integer'],
                'nombre' => ['required', 'string', 'max:100'],
                'especie_id' => ['required', 'integer', Rule::exists('especies', 'id_especie')],
                'raza_id' => ['nullable', 'integer', Rule::exists('razas', 'id_raza')],
                'otra_raza' => ['nullable', 'string', 'max:80'],
                'edad_anios' => ['nullable', 'integer', 'min:0', 'max:30'],
                'sexo' => ['required', Rule::in(['MACHO', 'HEMBRA', 'DESCONOCIDO'])],
                'tamano' => ['required', Rule::in(['CHICO', 'MEDIANO', 'GRANDE', 'DESCONOCIDO'])],
                'color_predominante' => ['nullable', 'string', 'max:120'],
                'descripcion' => ['required', 'string'],
                'vacunas_aplicadas' => ['nullable', 'string'],
                'esterilizado' => ['nullable', 'boolean'],
                'condicion_salud' => ['nullable', 'string', 'max:120'],
                'descripcion_salud' => ['nullable', 'string'],
                'requisitos' => ['nullable', 'string'],
                'colonia_barrio' => ['nullable', 'string', 'max:120'],
                'calle_referencias' => ['nullable', 'string', 'max:255'],
                'latitud' => ['nullable', 'numeric', 'between:-90,90'],
                'longitud' => ['nullable', 'numeric', 'between:-180,180'],
                'fotos' => ['nullable', 'array', 'max:8'],
                'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            ], [
                'nombre.required' => 'El nombre de la mascota es obligatorio.',
                'especie_id.required' => 'Debes seleccionar una especie.',
                'especie_id.exists' => 'La especie seleccionada no existe.',
                'raza_id.exists' => 'La raza seleccionada no existe.',
                'edad_anios.integer' => 'La edad debe ser un número entero.',
                'edad_anios.min' => 'La edad no puede ser negativa.',
                'edad_anios.max' => 'La edad no debe ser mayor a 30 años.',
                'sexo.required' => 'Debes seleccionar el sexo.',
                'sexo.in' => 'El sexo seleccionado no es válido.',
                'tamano.required' => 'Debes seleccionar el tamaño.',
                'tamano.in' => 'El tamaño seleccionado no es válido.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'fotos.max' => 'Solo puedes subir hasta 8 fotografías.',
                'fotos.*.image' => 'Cada archivo debe ser una imagen válida.',
                'fotos.*.mimes' => 'Cada fotografía debe estar en formato JPG, JPEG, PNG o WEBP.',
                'fotos.*.max' => 'Cada fotografía debe pesar máximo 10 MB.',
            ]);

            if ($request->filled('raza_id')) {
                $raza = Raza::find($request->raza_id);

                if (!$raza || (int) $raza->especie_id !== (int) $request->especie_id) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'La raza seleccionada no corresponde a la especie elegida.',
                        'errors' => [
                            'raza_id' => ['La raza seleccionada no corresponde a la especie elegida.'],
                        ],
                    ], 422);
                }
            }

            $archivos = $this->extraerArchivosFotos($request);

            if (count($archivos) === 0) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Debes subir al menos una fotografía.',
                    'errors' => [
                        'fotos' => ['Debes subir al menos una fotografía.'],
                    ],
                ], 422);
            }

            $adopcion = new PublicacionAdopcion();
            $adopcion->autor_usuario_id = (int) $request->autor_usuario_id;
            $adopcion->nombre = trim($request->nombre);
            $adopcion->especie_id = $request->especie_id;
            $adopcion->raza_id = $request->filled('raza_id') ? $request->raza_id : null;
            $adopcion->otra_raza = $request->filled('raza_id')
                ? null
                : ($request->filled('otra_raza') ? trim($request->otra_raza) : null);
            $adopcion->edad_anios = $request->filled('edad_anios') ? $request->edad_anios : null;
            $adopcion->sexo = $request->sexo;
            $adopcion->tamano = $request->tamano;
            $adopcion->color_predominante = $request->filled('color_predominante')
                ? trim($request->color_predominante)
                : null;
            $adopcion->descripcion = trim($request->descripcion);
            $adopcion->vacunas_aplicadas = $request->filled('vacunas_aplicadas')
                ? trim($request->vacunas_aplicadas)
                : null;
            $adopcion->esterilizado = $request->filled('esterilizado')
                ? (int) $request->esterilizado
                : 0;
            $adopcion->condicion_salud = $request->filled('condicion_salud')
                ? trim($request->condicion_salud)
                : null;
            $adopcion->descripcion_salud = $request->filled('descripcion_salud')
                ? trim($request->descripcion_salud)
                : null;
            $adopcion->requisitos = $request->filled('requisitos')
                ? trim($request->requisitos)
                : null;
            $adopcion->colonia_barrio = $request->filled('colonia_barrio')
                ? trim($request->colonia_barrio)
                : null;
            $adopcion->calle_referencias = $request->filled('calle_referencias')
                ? trim($request->calle_referencias)
                : null;
            $adopcion->latitud = $request->filled('latitud') ? $request->latitud : null;
            $adopcion->longitud = $request->filled('longitud') ? $request->longitud : null;
            $adopcion->estado = 'DISPONIBLE';
            $adopcion->save();

            foreach ($archivos as $index => $archivo) {
                $ruta = $archivo->store('adopciones', 'public');

                AdopcionFoto::create([
                    'publicacion_id' => $adopcion->id_publicacion,
                    'url' => $ruta,
                    'orden' => $index + 1,
                ]);
            }

            return response()->json([
                'ok' => true,
                'message' => 'Mascota publicada para adopción correctamente.',
                'data' => [
                    'id_publicacion' => $adopcion->id_publicacion,
                    'estado' => $adopcion->estado,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Revisa los campos del formulario.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo publicar la mascota en adopción.',
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

    private function flattenFiles($files): array
    {
        $resultado = [];

        if ($files instanceof UploadedFile) {
            $resultado[] = $files;
            return $resultado;
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                $resultado = array_merge($resultado, $this->flattenFiles($file));
            }
        }

        return $resultado;
    }

    private function extraerArchivosFotos(Request $request): array
    {
        $archivos = [];

        foreach (['fotos', 'fotos[]', 'foto'] as $campo) {
            $valor = $request->file($campo);

            if ($valor) {
                $archivos = array_merge($archivos, $this->flattenFiles($valor));
            }
        }

        if (empty($archivos)) {
            foreach ($request->allFiles() as $valor) {
                $archivos = array_merge($archivos, $this->flattenFiles($valor));
            }
        }

        return array_values(array_filter($archivos, function ($archivo) {
            return $archivo instanceof UploadedFile;
        }));
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