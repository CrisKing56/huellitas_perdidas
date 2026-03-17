<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;

class MobilePublicacionController extends Controller
{
    public function extravios()
    {
        $extravios = PublicacionExtravio::with(['fotoPrincipal', 'fotos', 'ubicacion', 'autor'])
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
            });

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

        $data = [
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

        return response()->json([
            'ok' => true,
            'data' => $data
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
}