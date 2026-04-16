<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function perfil()
    {
        $authUser = Auth::user();
        abort_if(!$authUser, 404);

        // IMPORTANTE:
        // No usar Auth::id() porque en tu proyecto puede no devolver id_usuario.
        $userId = $authUser->id_usuario;

        $query = DB::table('usuarios as u');
        $tieneConfig = Schema::hasTable('usuario_configuracion');

        if ($tieneConfig) {
            $query->leftJoin('usuario_configuracion as c', 'c.usuario_id', '=', 'u.id_usuario');
        }

        $selects = [
            'u.id_usuario',
            'u.nombre',
            'u.correo',
            Schema::hasColumn('usuarios', 'telefono') ? 'u.telefono' : DB::raw('NULL as telefono'),
            Schema::hasColumn('usuarios', 'whatsapp') ? 'u.whatsapp' : DB::raw('NULL as whatsapp'),
            Schema::hasColumn('usuarios', 'ciudad') ? 'u.ciudad' : DB::raw('NULL as ciudad'),
            Schema::hasColumn('usuarios', 'foto_perfil') ? 'u.foto_perfil' : DB::raw('NULL as foto_perfil'),
            Schema::hasColumn('usuarios', 'creado_en') ? 'u.creado_en' : DB::raw('NULL as creado_en'),
        ];

        if ($tieneConfig) {
            $selects[] = Schema::hasColumn('usuario_configuracion', 'recibir_notificaciones')
                ? DB::raw('COALESCE(c.recibir_notificaciones, 1) as recibir_notificaciones')
                : DB::raw('1 as recibir_notificaciones');

            $selects[] = Schema::hasColumn('usuario_configuracion', 'mostrar_telefono_publico')
                ? DB::raw('COALESCE(c.mostrar_telefono_publico, 0) as mostrar_telefono_publico')
                : DB::raw('0 as mostrar_telefono_publico');

            $selects[] = Schema::hasColumn('usuario_configuracion', 'mostrar_whatsapp_publico')
                ? DB::raw('COALESCE(c.mostrar_whatsapp_publico, 0) as mostrar_whatsapp_publico')
                : DB::raw('0 as mostrar_whatsapp_publico');

            $selects[] = Schema::hasColumn('usuario_configuracion', 'ocultar_ubicacion_exacta')
                ? DB::raw('COALESCE(c.ocultar_ubicacion_exacta, 1) as ocultar_ubicacion_exacta')
                : DB::raw('1 as ocultar_ubicacion_exacta');
        } else {
            $selects[] = DB::raw('1 as recibir_notificaciones');
            $selects[] = DB::raw('0 as mostrar_telefono_publico');
            $selects[] = DB::raw('0 as mostrar_whatsapp_publico');
            $selects[] = DB::raw('1 as ocultar_ubicacion_exacta');
        }

        $usuario = $query
            ->where('u.id_usuario', $userId)
            ->select($selects)
            ->first();

        abort_if(!$usuario, 404);

        $conteoExtravios = 0;
        if (Schema::hasTable('publicaciones_extravio')) {
            $conteoExtraviosQuery = DB::table('publicaciones_extravio')
                ->where('autor_usuario_id', $userId);

            if (Schema::hasColumn('publicaciones_extravio', 'estado')) {
                $conteoExtraviosQuery->where('estado', '!=', 'ELIMINADA');
            }

            $conteoExtravios = $conteoExtraviosQuery->count();
        }

        $conteoAdopciones = 0;
        if (Schema::hasTable('publicaciones_adopcion')) {
            $conteoAdopcionesQuery = DB::table('publicaciones_adopcion')
                ->where('autor_usuario_id', $userId);

            if (Schema::hasColumn('publicaciones_adopcion', 'estado')) {
                $conteoAdopcionesQuery->where('estado', '!=', 'ELIMINADA');
            }

            $conteoAdopciones = $conteoAdopcionesQuery->count();
        }

        $conteoComentarios = 0;
        if (Schema::hasTable('comentarios_extravio')) {
            $conteoComentariosQuery = DB::table('comentarios_extravio')
                ->where('usuario_id', $userId);

            if (Schema::hasColumn('comentarios_extravio', 'estado')) {
                $conteoComentariosQuery->where('estado', '!=', 'ELIMINADO');
            }

            $conteoComentarios = $conteoComentariosQuery->count();
        }

        $publicacionesExtravio = collect();
        if (Schema::hasTable('publicaciones_extravio')) {
            $queryExtravios = DB::table('publicaciones_extravio as p')
                ->leftJoin('extravio_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                         ->where('f.orden', '=', 1);
                })
                ->where('p.autor_usuario_id', $userId);

            if (Schema::hasColumn('publicaciones_extravio', 'estado')) {
                $queryExtravios->where('p.estado', '!=', 'ELIMINADA');
            }

            $publicacionesExtravio = $queryExtravios
                ->select(
                    'p.id_publicacion',
                    'p.nombre as titulo',
                    Schema::hasColumn('publicaciones_extravio', 'estado') ? 'p.estado' : DB::raw("'ACTIVA' as estado"),
                    Schema::hasColumn('publicaciones_extravio', 'creado_en')
                        ? 'p.creado_en as fecha'
                        : (Schema::hasColumn('publicaciones_extravio', 'created_at')
                            ? 'p.created_at as fecha'
                            : DB::raw('NULL as fecha')),
                    'f.url as imagen',
                    DB::raw("'Mascota extraviada' as tipo"),
                    DB::raw("'extravios.show' as ruta")
                )
                ->get();
        }

        $publicacionesAdopcion = collect();
        if (Schema::hasTable('publicaciones_adopcion')) {
            $queryAdopciones = DB::table('publicaciones_adopcion as p')
                ->leftJoin('adopcion_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                         ->where('f.orden', '=', 1);
                })
                ->where('p.autor_usuario_id', $userId);

            if (Schema::hasColumn('publicaciones_adopcion', 'estado')) {
                $queryAdopciones->where('p.estado', '!=', 'ELIMINADA');
            }

            $publicacionesAdopcion = $queryAdopciones
                ->select(
                    'p.id_publicacion',
                    'p.nombre as titulo',
                    Schema::hasColumn('publicaciones_adopcion', 'estado') ? 'p.estado' : DB::raw("'DISPONIBLE' as estado"),
                    Schema::hasColumn('publicaciones_adopcion', 'creado_en')
                        ? 'p.creado_en as fecha'
                        : (Schema::hasColumn('publicaciones_adopcion', 'created_at')
                            ? 'p.created_at as fecha'
                            : DB::raw('NULL as fecha')),
                    'f.url as imagen',
                    DB::raw("'Mascota en adopción' as tipo"),
                    DB::raw("'adopciones.show' as ruta")
                )
                ->get();
        }

        $publicaciones = $publicacionesExtravio
            ->merge($publicacionesAdopcion)
            ->sortByDesc('fecha')
            ->take(6)
            ->map(function ($pub) {
                $pub->url = route($pub->ruta, $pub->id_publicacion);
                return $pub;
            })
            ->values();

        $comentarios = collect();
        if (Schema::hasTable('comentarios_extravio') && Schema::hasTable('publicaciones_extravio')) {
            $queryComentarios = DB::table('comentarios_extravio as c')
                ->join('publicaciones_extravio as p', 'p.id_publicacion', '=', 'c.publicacion_id')
                ->where('c.usuario_id', $userId);

            if (Schema::hasColumn('comentarios_extravio', 'estado')) {
                $queryComentarios->where('c.estado', '!=', 'ELIMINADO');
            }

            $comentarios = $queryComentarios
                ->select(
                    'c.id_comentario',
                    Schema::hasColumn('comentarios_extravio', 'comentario') ? 'c.comentario as texto' : DB::raw("'' as texto"),
                    'p.nombre as contexto',
                    'p.id_publicacion',
                    Schema::hasColumn('comentarios_extravio', 'creado_en')
                        ? 'c.creado_en as fecha'
                        : (Schema::hasColumn('comentarios_extravio', 'created_at')
                            ? 'c.created_at as fecha'
                            : DB::raw('NULL as fecha'))
                )
                ->orderByDesc('fecha')
                ->limit(6)
                ->get()
                ->map(function ($com) {
                    $com->url = route('extravios.show', $com->id_publicacion) . '#comentarios';
                    return $com;
                });
        }

        return view('perfil.perfil', compact(
            'usuario',
            'publicaciones',
            'comentarios',
            'conteoExtravios',
            'conteoAdopciones',
            'conteoComentarios'
        ));
    }

    public function update(Request $request)
    {
        $authUser = Auth::user();
        abort_if(!$authUser, 404);

        $request->validate([
            'nombre' => 'required|string|max:120',
            'telefono' => 'nullable|digits:10',
            'whatsapp' => 'nullable|digits:10',
            'ciudad' => 'nullable|string|max:100',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
            'whatsapp.digits' => 'El WhatsApp debe tener 10 dígitos.',
            'ciudad.max' => 'La ciudad no debe exceder 100 caracteres.',
        ]);

        $data = [
            'nombre' => trim($request->nombre),
        ];

        if (Schema::hasColumn('usuarios', 'telefono')) {
            $data['telefono'] = $request->telefono ?: null;
        }

        if (Schema::hasColumn('usuarios', 'whatsapp')) {
            $data['whatsapp'] = $request->whatsapp ?: null;
        }

        if (Schema::hasColumn('usuarios', 'ciudad')) {
            $data['ciudad'] = $request->ciudad ? trim($request->ciudad) : null;
        }

        if (Schema::hasColumn('usuarios', 'actualizado_en')) {
            $data['actualizado_en'] = now();
        }

        DB::table('usuarios')
            ->where('id_usuario', $authUser->id_usuario)
            ->update($data);

        return back()->with('success_profile', 'Tu información fue actualizada correctamente.');
    }

    public function updatePhoto(Request $request)
    {
        $authUser = Auth::user();
        abort_if(!$authUser, 404);

        $request->validate([
            'foto_perfil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_perfil.required' => 'Debes seleccionar una imagen.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 2 MB.',
        ]);

        if (!Schema::hasColumn('usuarios', 'foto_perfil')) {
            return back()->withErrors([
                'foto_perfil' => 'La columna foto_perfil no existe en la tabla usuarios.'
            ]);
        }

        $usuarioActual = DB::table('usuarios')
            ->where('id_usuario', $authUser->id_usuario)
            ->first();

        if ($usuarioActual && !empty($usuarioActual->foto_perfil)) {
            $fotoAnterior = $usuarioActual->foto_perfil;

            if (!Str::startsWith($fotoAnterior, ['http://', 'https://'])) {
                Storage::disk('public')->delete($fotoAnterior);
            }
        }

        $ruta = $request->file('foto_perfil')->store('perfiles', 'public');

        $data = [
            'foto_perfil' => $ruta,
        ];

        if (Schema::hasColumn('usuarios', 'actualizado_en')) {
            $data['actualizado_en'] = now();
        }

        DB::table('usuarios')
            ->where('id_usuario', $authUser->id_usuario)
            ->update($data);

        return back()->with('success_photo', 'Foto de perfil actualizada correctamente.');
    }

    public function updateSettings(Request $request)
    {
        $authUser = Auth::user();
        abort_if(!$authUser, 404);

        if (!Schema::hasTable('usuario_configuracion')) {
            return back()->withErrors([
                'configuracion' => 'La tabla usuario_configuracion no existe.'
            ]);
        }

        $data = [
            'usuario_id' => $authUser->id_usuario,
            'recibir_notificaciones' => $request->has('recibir_notificaciones') ? 1 : 0,
            'mostrar_telefono_publico' => $request->has('mostrar_telefono_publico') ? 1 : 0,
            'mostrar_whatsapp_publico' => $request->has('mostrar_whatsapp_publico') ? 1 : 0,
            'ocultar_ubicacion_exacta' => $request->has('ocultar_ubicacion_exacta') ? 1 : 0,
        ];

        if (Schema::hasColumn('usuario_configuracion', 'actualizado_en')) {
            $data['actualizado_en'] = now();
        }

        DB::table('usuario_configuracion')->updateOrInsert(
            ['usuario_id' => $authUser->id_usuario],
            $data
        );

        return back()->with('success_settings', 'Configuración actualizada correctamente.');
    }
}