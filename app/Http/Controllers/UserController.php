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
        $userId = Auth::id();

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

        $conteoExtravios = Schema::hasTable('publicaciones_extravio')
            ? DB::table('publicaciones_extravio')
                ->where('autor_usuario_id', $userId)
                ->where('estado', '!=', 'ELIMINADA')
                ->count()
            : 0;

        $conteoAdopciones = Schema::hasTable('publicaciones_adopcion')
            ? DB::table('publicaciones_adopcion')
                ->where('autor_usuario_id', $userId)
                ->where('estado', '!=', 'ELIMINADA')
                ->count()
            : 0;

        $conteoComentarios = Schema::hasTable('comentarios_extravio')
            ? DB::table('comentarios_extravio')
                ->where('usuario_id', $userId)
                ->where('estado', '!=', 'ELIMINADO')
                ->count()
            : 0;

        $publicacionesExtravio = collect();
        if (Schema::hasTable('publicaciones_extravio')) {
            $publicacionesExtravio = DB::table('publicaciones_extravio as p')
                ->leftJoin('extravio_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                        ->where('f.orden', '=', 1);
                })
                ->where('p.autor_usuario_id', $userId)
                ->where('p.estado', '!=', 'ELIMINADA')
                ->select(
                    'p.id_publicacion',
                    'p.nombre as titulo',
                    'p.estado',
                    Schema::hasColumn('publicaciones_extravio', 'creado_en')
                        ? 'p.creado_en as fecha'
                        : DB::raw('NULL as fecha'),
                    'f.url as imagen',
                    DB::raw("'Mascota extraviada' as tipo"),
                    DB::raw("'extravios.show' as ruta")
                )
                ->get();
        }

        $publicacionesAdopcion = collect();
        if (Schema::hasTable('publicaciones_adopcion')) {
            $publicacionesAdopcion = DB::table('publicaciones_adopcion as p')
                ->leftJoin('adopcion_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                        ->where('f.orden', '=', 1);
                })
                ->where('p.autor_usuario_id', $userId)
                ->where('p.estado', '!=', 'ELIMINADA')
                ->select(
                    'p.id_publicacion',
                    'p.nombre as titulo',
                    'p.estado',
                    Schema::hasColumn('publicaciones_adopcion', 'creado_en')
                        ? 'p.creado_en as fecha'
                        : DB::raw('NULL as fecha'),
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
            $comentarios = DB::table('comentarios_extravio as c')
                ->join('publicaciones_extravio as p', 'p.id_publicacion', '=', 'c.publicacion_id')
                ->where('c.usuario_id', $userId)
                ->where('c.estado', '!=', 'ELIMINADO')
                ->select(
                    'c.id_comentario',
                    'c.comentario as texto',
                    'p.nombre as contexto',
                    'p.id_publicacion',
                    Schema::hasColumn('comentarios_extravio', 'creado_en')
                        ? 'c.creado_en as fecha'
                        : DB::raw('NULL as fecha')
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
            ->where('id_usuario', Auth::id())
            ->update($data);

        return back()->with('success_profile', 'Tu información fue actualizada correctamente.');
    }

    public function updatePhoto(Request $request)
    {
        if (!Schema::hasColumn('usuarios', 'foto_perfil')) {
            return back()->withErrors([
                'foto_perfil' => 'La base de datos actual no tiene el campo foto_perfil.'
            ]);
        }

        $request->validate([
            'foto_perfil' => 'required|image|max:5120',
        ], [
            'foto_perfil.required' => 'Selecciona una imagen.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 5 MB.',
        ]);

        $usuario = DB::table('usuarios')
            ->where('id_usuario', Auth::id())
            ->select('foto_perfil')
            ->first();

        if ($usuario && !empty($usuario->foto_perfil) && !Str::startsWith($usuario->foto_perfil, ['http://', 'https://'])) {
            $rutaAnterior = ltrim(str_replace('storage/', '', $usuario->foto_perfil), '/');
            if (Storage::disk('public')->exists($rutaAnterior)) {
                Storage::disk('public')->delete($rutaAnterior);
            }
        }

        $nuevaRuta = $request->file('foto_perfil')->store('perfiles', 'public');

        $data = ['foto_perfil' => $nuevaRuta];

        if (Schema::hasColumn('usuarios', 'actualizado_en')) {
            $data['actualizado_en'] = now();
        }

        DB::table('usuarios')
            ->where('id_usuario', Auth::id())
            ->update($data);

        return back()->with('success_photo', 'Tu foto de perfil fue actualizada correctamente.');
    }

    public function updateSettings(Request $request)
    {
        if (!Schema::hasTable('usuario_configuracion')) {
            return back()->withErrors([
                'configuracion' => 'La tabla usuario_configuracion no existe en esta base de datos.'
            ]);
        }

        $data = [];

        if (Schema::hasColumn('usuario_configuracion', 'recibir_notificaciones')) {
            $data['recibir_notificaciones'] = $request->boolean('recibir_notificaciones');
        }

        if (Schema::hasColumn('usuario_configuracion', 'recibir_correos')) {
            $data['recibir_correos'] = 1;
        }

        if (Schema::hasColumn('usuario_configuracion', 'mostrar_telefono_publico')) {
            $data['mostrar_telefono_publico'] = $request->boolean('mostrar_telefono_publico');
        }

        if (Schema::hasColumn('usuario_configuracion', 'mostrar_whatsapp_publico')) {
            $data['mostrar_whatsapp_publico'] = $request->boolean('mostrar_whatsapp_publico');
        }

        if (Schema::hasColumn('usuario_configuracion', 'ocultar_ubicacion_exacta')) {
            $data['ocultar_ubicacion_exacta'] = $request->boolean('ocultar_ubicacion_exacta');
        }

        if (Schema::hasColumn('usuario_configuracion', 'actualizado_en')) {
            $data['actualizado_en'] = now();
        }

        DB::table('usuario_configuracion')->updateOrInsert(
            ['usuario_id' => Auth::id()],
            $data
        );

        return back()->with('success_settings', 'La configuración del perfil fue actualizada.');
    }
}