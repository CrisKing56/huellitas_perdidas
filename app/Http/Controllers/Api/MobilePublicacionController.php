<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobilePublicacionController extends Controller
{
    public function especies()
    {
        $especies = DB::table('especies')
            ->where('activo', 1)
            ->orderBy('nombre')
            ->select('id_especie', 'nombre')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $especies
        ]);
    }

    public function razas(Request $request)
    {
        $request->validate([
            'especie_id' => 'required|exists:especies,id_especie',
        ]);

        $razas = DB::table('razas')
            ->where('especie_id', $request->especie_id)
            ->orderBy('nombre')
            ->select('id_raza', 'especie_id', 'nombre')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $razas
        ]);
    }

    public function extravios()
    {
        $extravios = PublicacionExtravio::with(['fotoPrincipal', 'fotos', 'ubicacion', 'autor'])
            ->orderByDesc('id_publicacion')
            ->get()
            ->map(fn($item) => $this->transformarExtravio($item));

        return response()->json([
            'ok' => true,
            'data' => $extravios
        ]);
    }

    public function misExtravios($idUsuario)
    {
        $extravios = PublicacionExtravio::with(['fotoPrincipal', 'fotos', 'ubicacion', 'autor'])
            ->where('autor_usuario_id', $idUsuario)
            ->orderByDesc('id_publicacion')
            ->get()
            ->map(fn($item) => $this->transformarExtravio($item));

        return response()->json([
            'ok' => true,
            'data' => $extravios
        ]);
    }

    public function detalleExtravio($id)
    {
        $item = PublicacionExtravio::with(['fotoPrincipal', 'fotos', 'ubicacion', 'autor'])
            ->where('id_publicacion', $id)
            ->first();

        if (!$item) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación no encontrada'
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $this->transformarExtravio($item)
        ]);
    }

    public function storeExtravio(Request $request)
    {
        $request->validate([
            'autor_usuario_id' => 'required|exists:usuarios,id_usuario',
            'nombre' => 'required|string|max:100',
            'especie_id' => 'required|exists:especies,id_especie',
            'raza_id' => 'nullable|integer|exists:razas,id_raza',
            'otra_raza' => 'nullable|string|max:80',
            'color' => 'required|string|max:80',
            'tamano' => 'required|in:PEQUEÑO,MEDIANO,GRANDE,PEQUEÃ‘O',
            'sexo' => 'required|in:MACHO,HEMBRA,DESCONOCIDO',
            'fecha_extravio' => 'required|date',
            'colonia_barrio' => 'required|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion' => 'required|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $razaId = $request->filled('raza_id') ? (int) $request->raza_id : null;
        $otraRaza = trim((string) $request->otra_raza);

        if (!$razaId && $otraRaza === '') {
            throw ValidationException::withMessages([
                'raza_id' => ['Debes seleccionar una raza o escribir otra raza.'],
            ]);
        }

        if ($razaId) {
            $razaValida = DB::table('razas')
                ->where('id_raza', $razaId)
                ->where('especie_id', $request->especie_id)
                ->exists();

            if (!$razaValida) {
                throw ValidationException::withMessages([
                    'raza_id' => ['La raza seleccionada no corresponde a la especie.'],
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $ubicacionId = null;

            if ($request->filled('latitud') && $request->filled('longitud')) {
                $ubicacionId = DB::table('ubicaciones')->insertGetId([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $publicacionId = DB::table('publicaciones_extravio')->insertGetId([
                'autor_usuario_id' => $request->autor_usuario_id,
                'autor_organizacion_id' => null,
                'nombre' => $request->nombre,
                'especie_id' => $request->especie_id,
                'raza_id' => $razaId,
                'otra_raza' => $razaId ? null : ($otraRaza !== '' ? $otraRaza : null),
                'color' => $request->color,
                'tamano' => $request->tamano === 'PEQUEÑO' ? 'PEQUEÃ‘O' : $request->tamano,
                'sexo' => $request->sexo,
                'fecha_extravio' => $request->fecha_extravio,
                'colonia_barrio' => $request->colonia_barrio,
                'calle_referencias' => $request->calle_referencias,
                'ubicacion_id' => $ubicacionId,
                'descripcion' => $request->descripcion,
                'estado' => 'ACTIVA',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->hasFile('fotos')) {
                $orden = 1;

                foreach ($request->file('fotos') as $foto) {
                    if (!$foto || !$foto->isValid()) {
                        continue;
                    }

                    $ruta = $foto->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacionId,
                        'url' => $ruta,
                        'orden' => $orden,
                    ]);

                    $orden++;
                }
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Reporte de extravío creado correctamente.',
                'id_publicacion' => $publicacionId
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo crear el reporte.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateExtravio(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::where('id_publicacion', $id)->first();

        if (!$publicacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación no encontrada'
            ], 404);
        }

        $request->validate([
            'autor_usuario_id' => 'required|exists:usuarios,id_usuario',
            'nombre' => 'required|string|max:100',
            'especie_id' => 'required|exists:especies,id_especie',
            'raza_id' => 'nullable|integer|exists:razas,id_raza',
            'otra_raza' => 'nullable|string|max:80',
            'color' => 'required|string|max:80',
            'tamano' => 'required|in:PEQUEÑO,MEDIANO,GRANDE,PEQUEÃ‘O',
            'sexo' => 'required|in:MACHO,HEMBRA,DESCONOCIDO',
            'fecha_extravio' => 'required|date',
            'colonia_barrio' => 'required|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion' => 'required|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ((int) $publicacion->autor_usuario_id !== (int) $request->autor_usuario_id) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para editar esta publicación.'
            ], 403);
        }

        $razaId = $request->filled('raza_id') ? (int) $request->raza_id : null;
        $otraRaza = trim((string) $request->otra_raza);

        if (!$razaId && $otraRaza === '') {
            throw ValidationException::withMessages([
                'raza_id' => ['Debes seleccionar una raza o escribir otra raza.'],
            ]);
        }

        if ($razaId) {
            $razaValida = DB::table('razas')
                ->where('id_raza', $razaId)
                ->where('especie_id', $request->especie_id)
                ->exists();

            if (!$razaValida) {
                throw ValidationException::withMessages([
                    'raza_id' => ['La raza seleccionada no corresponde a la especie.'],
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $ubicacionId = $publicacion->ubicacion_id;

            if ($request->filled('latitud') && $request->filled('longitud')) {
                if ($ubicacionId) {
                    DB::table('ubicaciones')
                        ->where('id_ubicacion', $ubicacionId)
                        ->update([
                            'latitud' => $request->latitud,
                            'longitud' => $request->longitud,
                            'updated_at' => now(),
                        ]);
                } else {
                    $ubicacionId = DB::table('ubicaciones')->insertGetId([
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('publicaciones_extravio')
                ->where('id_publicacion', $id)
                ->update([
                    'nombre' => $request->nombre,
                    'especie_id' => $request->especie_id,
                    'raza_id' => $razaId,
                    'otra_raza' => $razaId ? null : ($otraRaza !== '' ? $otraRaza : null),
                    'color' => $request->color,
                    'tamano' => $request->tamano === 'PEQUEÑO' ? 'PEQUEÃ‘O' : $request->tamano,
                    'sexo' => $request->sexo,
                    'fecha_extravio' => $request->fecha_extravio,
                    'colonia_barrio' => $request->colonia_barrio,
                    'calle_referencias' => $request->calle_referencias,
                    'ubicacion_id' => $ubicacionId,
                    'descripcion' => $request->descripcion,
                    'updated_at' => now(),
                ]);

            if ($request->hasFile('fotos')) {
                DB::table('extravio_fotos')
                    ->where('publicacion_id', $id)
                    ->delete();

                $orden = 1;

                foreach ($request->file('fotos') as $foto) {
                    if (!$foto || !$foto->isValid()) {
                        continue;
                    }

                    $ruta = $foto->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $id,
                        'url' => $ruta,
                        'orden' => $orden,
                    ]);

                    $orden++;
                }
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Reporte actualizado correctamente.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo actualizar el reporte.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteExtravio(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::where('id_publicacion', $id)->first();

        if (!$publicacion) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación no encontrada'
            ], 404);
        }

        $request->validate([
            'autor_usuario_id' => 'required|exists:usuarios,id_usuario',
        ]);

        if ((int) $publicacion->autor_usuario_id !== (int) $request->autor_usuario_id) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para eliminar esta publicación.'
            ], 403);
        }

        DB::table('publicaciones_extravio')
            ->where('id_publicacion', $id)
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Reporte eliminado correctamente.'
        ]);
    }

    public function adopciones()
    {
        $adopciones = PublicacionAdopcion::with(['fotoPrincipal', 'fotos', 'autor'])
            ->orderByDesc('id_publicacion')
            ->get()
            ->map(function ($item) {
                return [
                    'id_publicacion' => $item->id_publicacion,
                    'autor_usuario_id' => $item->autor_usuario_id,
                    'autor_organizacion_id' => $item->autor_organizacion_id,
                    'nombre' => $item->nombre,
                    'especie_id' => $item->especie_id,
                    'raza_id' => $item->raza_id,
                    'otra_raza' => $item->otra_raza,
                    'edad_anios' => $item->edad_anios,
                    'sexo' => $item->sexo,
                    'tamano' => $item->tamano,
                    'color_predominante' => $item->color_predominante,
                    'descripcion' => $item->descripcion,
                    'vacunas_aplicadas' => $item->vacunas_aplicadas,
                    'esterilizado' => $item->esterilizado,
                    'condicion_salud' => $item->condicion_salud,
                    'descripcion_salud' => $item->descripcion_salud,
                    'requisitos' => $item->requisitos,
                    'colonia_barrio' => $item->colonia_barrio,
                    'calle_referencias' => $item->calle_referencias,
                    'estado' => $item->estado,

                    'foto_principal' => $item->fotoPrincipal ? [
                        'id_foto' => $item->fotoPrincipal->id_foto,
                        'url' => $item->fotoPrincipal->url,
                        'url_completa' => asset('storage/' . $item->fotoPrincipal->url),
                        'orden' => $item->fotoPrincipal->orden,
                    ] : null,

                    'fotos' => $item->fotos->map(function ($foto) {
                        return [
                            'id_foto' => $foto->id_foto,
                            'url' => $foto->url,
                            'url_completa' => asset('storage/' . $foto->url),
                            'orden' => $foto->orden,
                        ];
                    })->values(),

                    'autor' => $item->autor ? [
                        'id_usuario' => $item->autor->id_usuario,
                        'nombre' => $item->autor->nombre,
                        'correo' => $item->autor->correo,
                        'telefono' => $item->autor->telefono,
                        'rol' => $item->autor->rol,
                    ] : null,
                ];
            });

        return response()->json([
            'ok' => true,
            'data' => $adopciones
        ]);
    }

    private function transformarExtravio($item)
    {
        return [
            'id_publicacion' => $item->id_publicacion,
            'autor_usuario_id' => $item->autor_usuario_id,
            'autor_organizacion_id' => $item->autor_organizacion_id,
            'nombre' => $item->nombre,
            'especie_id' => $item->especie_id,
            'raza_id' => $item->raza_id,
            'otra_raza' => $item->otra_raza,
            'color' => $item->color,
            'tamano' => $item->tamano,
            'sexo' => $item->sexo,
            'fecha_extravio' => $item->fecha_extravio,
            'colonia_barrio' => $item->colonia_barrio,
            'calle_referencias' => $item->calle_referencias,
            'descripcion' => $item->descripcion,
            'estado' => $item->estado,

            'foto_principal' => $item->fotoPrincipal ? [
                'id_foto' => $item->fotoPrincipal->id_foto,
                'url' => $item->fotoPrincipal->url,
                'url_completa' => asset('storage/' . $item->fotoPrincipal->url),
                'orden' => $item->fotoPrincipal->orden,
            ] : null,

            'fotos' => $item->fotos->map(function ($foto) {
                return [
                    'id_foto' => $foto->id_foto,
                    'url' => $foto->url,
                    'url_completa' => asset('storage/' . $foto->url),
                    'orden' => $foto->orden,
                ];
            })->values(),

            'ubicacion' => $item->ubicacion ? [
                'id_ubicacion' => $item->ubicacion->id_ubicacion,
                'latitud' => $item->ubicacion->latitud,
                'longitud' => $item->ubicacion->longitud,
            ] : null,

            'autor' => $item->autor ? [
                'id_usuario' => $item->autor->id_usuario,
                'nombre' => $item->autor->nombre,
                'correo' => $item->autor->correo,
                'telefono' => $item->autor->telefono,
                'rol' => $item->autor->rol,
            ] : null,
        ];
    }
    
    public function detalleAdopcion(Request $request, $id)
    {
        $idUsuario = (int) $request->query('id_usuario', 0);

        $adopcion = DB::table('publicaciones_adopcion as p')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'p.raza_id')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'p.autor_usuario_id')
            ->leftJoin('organizaciones as o', 'o.id_organizacion', '=', 'p.autor_organizacion_id')
            ->where('p.id_publicacion', $id)
            ->select(
                'p.*',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre',
                'u.id_usuario as autor_usuario_id_real',
                'u.nombre as autor_usuario_nombre',
                'u.telefono as autor_usuario_telefono',
                'u.whatsapp as autor_usuario_whatsapp',
                'u.correo as autor_usuario_correo',
                'o.id_organizacion as autor_organizacion_id_real',
                'o.nombre as autor_organizacion_nombre',
                'o.telefono as autor_organizacion_telefono',
                'o.whatsapp as autor_organizacion_whatsapp',
                'o.sitio_web as autor_organizacion_sitio_web'
            )
            ->first();

        if (!$adopcion) {
            return response()->json([
                'ok' => false,
                'message' => 'Publicación de adopción no encontrada',
            ], 404);
        }

        $organizacionIdsUsuario = [];

        if ($idUsuario > 0) {
            $organizacionIdsUsuario = DB::table('organizaciones')
                ->where('usuario_dueno_id', $idUsuario)
                ->pluck('id_organizacion')
                ->map(fn ($idOrg) => (int) $idOrg)
                ->toArray();
        }

        $esAutor = false;

        if ($idUsuario > 0) {
            $esAutor =
                ((int) ($adopcion->autor_usuario_id ?? 0) === $idUsuario) ||
                in_array((int) ($adopcion->autor_organizacion_id ?? 0), $organizacionIdsUsuario, true);
        }

        $solicitudUsuario = null;

        $solicitudUsuario = null;

        if ($idUsuario > 0 && !$esAutor) {
            $solicitudUsuario = \App\Models\SolicitudAdopcion::where('publicacion_id', $id)
                ->where('solicitante_usuario_id', $idUsuario)
                ->latest('id_solicitud')
                ->first();
        }

        $estadoSolicitud = strtoupper((string) ($solicitudUsuario->estado ?? ''));
        $puedeVerContacto = $esAutor || $estadoSolicitud === 'ACEPTADA';
        $puedeSolicitar = !$esAutor
            && strtoupper((string) ($adopcion->estado ?? '')) === 'DISPONIBLE'
            && !$solicitudUsuario;

        $estadoSolicitud = strtoupper((string) ($solicitudUsuario->estado ?? ''));
        $puedeVerContacto = $esAutor || in_array($estadoSolicitud, ['SELECCIONADA', 'APROBADA', 'ACEPTADA'], true);
        $puedeSolicitar = !$esAutor
            && strtoupper((string) ($adopcion->estado ?? '')) === 'DISPONIBLE'
            && !$solicitudUsuario;

        $fotos = DB::table('adopcion_fotos')
            ->where('publicacion_id', $id)
            ->orderBy('orden')
            ->get()
            ->map(function ($foto) {
                return [
                    'id_foto' => $foto->id_foto,
                    'url' => $foto->url,
                    'url_completa' => asset('storage/' . ltrim($foto->url, '/')),
                    'orden' => $foto->orden,
                ];
            })
            ->values();

        $fotoPrincipal = $fotos->first();

        $nombreAutor = $adopcion->autor_organizacion_nombre ?: $adopcion->autor_usuario_nombre ?: 'Usuario';
        $telefonoAutor = $adopcion->autor_organizacion_telefono ?: $adopcion->autor_usuario_telefono;
        $whatsappAutor = $adopcion->autor_organizacion_whatsapp ?: $adopcion->autor_usuario_whatsapp;
        $correoAutor = $adopcion->autor_usuario_correo;
        $sitioWebAutor = $adopcion->autor_organizacion_sitio_web;

        return response()->json([
            'ok' => true,
            'data' => [
                'id_publicacion' => (int) $adopcion->id_publicacion,
                'autor_usuario_id' => $adopcion->autor_usuario_id,
                'autor_organizacion_id' => $adopcion->autor_organizacion_id,
                'nombre' => $adopcion->nombre,
                'especie_id' => $adopcion->especie_id,
                'especie_nombre' => $adopcion->especie_nombre,
                'raza_id' => $adopcion->raza_id,
                'raza_nombre' => $adopcion->raza_nombre,
                'otra_raza' => $adopcion->otra_raza,
                'edad_anios' => $adopcion->edad_anios,
                'sexo' => $adopcion->sexo,
                'tamano' => $adopcion->tamano,
                'color_predominante' => $adopcion->color_predominante,
                'vacunas_aplicadas' => $adopcion->vacunas_aplicadas,
                'esterilizado' => $adopcion->esterilizado,
                'condicion_salud' => $adopcion->condicion_salud,
                'descripcion_salud' => $adopcion->descripcion_salud,
                'requisitos' => $adopcion->requisitos,
                'colonia_barrio' => $adopcion->colonia_barrio,
                'calle_referencias' => $adopcion->calle_referencias,
                'latitud' => $adopcion->latitud,
                'longitud' => $adopcion->longitud,
                'descripcion' => $adopcion->descripcion,
                'estado' => $adopcion->estado,
                'created_at' => $adopcion->created_at,
                'contacto_visible' => $puedeVerContacto,
                'es_autor' => $esAutor,
                'puede_solicitar' => $puedeSolicitar,
                'estado_solicitud' => $solicitudUsuario->estado ?? null,
                'autor' => [
                    'nombre' => $nombreAutor,
                    'telefono' => $puedeVerContacto ? $telefonoAutor : null,
                    'whatsapp' => $puedeVerContacto ? $whatsappAutor : null,
                    'correo' => $puedeVerContacto ? $correoAutor : null,
                    'sitio_web' => $puedeVerContacto ? $sitioWebAutor : null,
                ],
                'foto_principal' => $fotoPrincipal,
                'foto_principal_url' => $fotoPrincipal['url_completa'] ?? null,
                'fotos' => $fotos,
            ],
        ]);
    }

    public function misAdopciones($idUsuario)
    {
        $idUsuario = (int) $idUsuario;

        $organizacionIds = DB::table('organizaciones')
            ->where('usuario_dueno_id', $idUsuario)
            ->pluck('id_organizacion')
            ->map(fn ($idOrg) => (int) $idOrg)
            ->toArray();

        $publicaciones = DB::table('publicaciones_adopcion as p')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->where(function ($query) use ($idUsuario, $organizacionIds) {
                $query->where('p.autor_usuario_id', $idUsuario);

                if (!empty($organizacionIds)) {
                    $query->orWhereIn('p.autor_organizacion_id', $organizacionIds);
                }
            })
            ->orderByDesc('p.id_publicacion')
            ->select(
                'p.id_publicacion',
                'p.nombre',
                'p.descripcion',
                'p.colonia_barrio',
                'p.estado',
                'p.edad_anios',
                'p.sexo',
                'p.tamano',
                'p.color_predominante',
                'p.otra_raza',
                'p.created_at',
                'e.nombre as especie_nombre'
            )
            ->get()
            ->map(function ($item) {
                $foto = DB::table('adopcion_fotos')
                    ->where('publicacion_id', $item->id_publicacion)
                    ->orderBy('orden')
                    ->first();

                return [
                    'id_publicacion' => (int) $item->id_publicacion,
                    'nombre' => $item->nombre,
                    'descripcion' => $item->descripcion,
                    'colonia_barrio' => $item->colonia_barrio,
                    'estado' => $item->estado,
                    'edad_anios' => $item->edad_anios,
                    'sexo' => $item->sexo,
                    'tamano' => $item->tamano,
                    'color_predominante' => $item->color_predominante,
                    'otra_raza' => $item->otra_raza,
                    'especie_nombre' => $item->especie_nombre,
                    'created_at' => $item->created_at,
                    'foto_principal' => $foto ? [
                        'id_foto' => $foto->id_foto,
                        'url' => $foto->url,
                        'url_completa' => asset('storage/' . ltrim($foto->url, '/')),
                    ] : null,
                    'foto_principal_url' => $foto
                        ? asset('storage/' . ltrim($foto->url, '/'))
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $publicaciones,
        ]);
    }
}
