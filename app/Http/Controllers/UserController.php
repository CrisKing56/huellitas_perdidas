<?php

namespace App\Http\Controllers;

use App\Models\PublicacionAdopcion;
use App\Models\PublicacionExtravio;
use App\Models\SolicitudAdopcion;
use App\Models\UsuarioConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    private function usuarioActual()
    {
        return Auth::user();
    }

    private function configuracionUsuario(int $usuarioId): UsuarioConfiguracion
    {
        return UsuarioConfiguracion::firstOrCreate(
            ['usuario_id' => $usuarioId],
            [
                'recibir_notificaciones' => 1,
                'recibir_correos' => 1,
                'mostrar_telefono_publico' => 0,
                'mostrar_whatsapp_publico' => 0,
                'ocultar_ubicacion_exacta' => 1,
            ]
        );
    }

    private function organizacionActual(int $usuarioId)
    {
        return DB::table('organizaciones as o')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->select(
                'o.*',
                'd.calle_numero',
                'd.colonia',
                'd.codigo_postal',
                'd.ciudad as ciudad_direccion',
                'd.estado as estado_direccion'
            )
            ->where('o.usuario_dueno_id', $usuarioId)
            ->first();
    }

    private function contarExtravios(int $usuarioId, $organizacion = null): int
    {
        return PublicacionExtravio::query()
            ->when($organizacion, function ($query) use ($usuarioId, $organizacion) {
                $query->where(function ($sub) use ($usuarioId, $organizacion) {
                    $sub->where('autor_usuario_id', $usuarioId)
                        ->orWhere('autor_organizacion_id', $organizacion->id_organizacion);
                });
            }, function ($query) use ($usuarioId) {
                $query->where('autor_usuario_id', $usuarioId);
            })
            ->count();
    }

    private function contarAdopciones(int $usuarioId, $organizacion = null): int
    {
        return PublicacionAdopcion::query()
            ->when($organizacion, function ($query) use ($usuarioId, $organizacion) {
                $query->where(function ($sub) use ($usuarioId, $organizacion) {
                    $sub->where('autor_usuario_id', $usuarioId)
                        ->orWhere('autor_organizacion_id', $organizacion->id_organizacion);
                });
            }, function ($query) use ($usuarioId) {
                $query->where('autor_usuario_id', $usuarioId);
            })
            ->count();
    }

    private function contarComentarios(int $usuarioId): int
    {
        return DB::table('comentarios_extravio')
            ->where('usuario_id', $usuarioId)
            ->where('estado', '<>', 'ELIMINADO')
            ->count();
    }

    private function contarSolicitudesEnviadas(int $usuarioId): int
    {
        return SolicitudAdopcion::where('solicitante_usuario_id', $usuarioId)->count();
    }

    private function contarConsejos($organizacion): int
    {
        if (!$organizacion) {
            return 0;
        }

        return DB::table('consejos')
            ->where('autor_organizacion_id', $organizacion->id_organizacion)
            ->count();
    }

    private function contarSolicitudesRecibidas(int $usuarioId, $organizacion = null): int
    {
        return DB::table('solicitudes_adopcion as s')
            ->join('publicaciones_adopcion as p', 'p.id_publicacion', '=', 's.publicacion_id')
            ->where(function ($query) use ($usuarioId, $organizacion) {
                $query->where('p.autor_usuario_id', $usuarioId);

                if ($organizacion) {
                    $query->orWhere('p.autor_organizacion_id', $organizacion->id_organizacion);
                }
            })
            ->count();
    }

    private function cargarPublicacionesRecientes(int $usuarioId, $organizacion = null): Collection
    {
        $items = collect();

        $extravios = DB::table('publicaciones_extravio as p')
            ->select(
                'p.id_publicacion',
                'p.nombre as titulo',
                'p.estado',
                'p.created_at as fecha',
                'p.descripcion',
                DB::raw("'Extravío' as tipo"),
                DB::raw("(SELECT ef.url FROM extravio_fotos ef WHERE ef.publicacion_id = p.id_publicacion ORDER BY ef.orden ASC LIMIT 1) as imagen")
            )
            ->where(function ($query) use ($usuarioId, $organizacion) {
                $query->where('p.autor_usuario_id', $usuarioId);

                if ($organizacion) {
                    $query->orWhere('p.autor_organizacion_id', $organizacion->id_organizacion);
                }
            })
            ->orderByDesc('p.created_at')
            ->limit(6)
            ->get();

        foreach ($extravios as $item) {
            $items->push((object) [
                'titulo' => $item->titulo,
                'tipo' => $item->tipo,
                'estado' => $item->estado,
                'fecha' => $item->fecha,
                'imagen' => $item->imagen,
                'url' => route('extravios.show', $item->id_publicacion),
            ]);
        }

        $adopciones = DB::table('publicaciones_adopcion as p')
            ->select(
                'p.id_publicacion',
                'p.nombre as titulo',
                'p.estado',
                'p.created_at as fecha',
                'p.descripcion',
                DB::raw("'Adopción' as tipo"),
                DB::raw("(SELECT af.url FROM adopcion_fotos af WHERE af.publicacion_id = p.id_publicacion ORDER BY af.orden ASC LIMIT 1) as imagen")
            )
            ->where(function ($query) use ($usuarioId, $organizacion) {
                $query->where('p.autor_usuario_id', $usuarioId);

                if ($organizacion) {
                    $query->orWhere('p.autor_organizacion_id', $organizacion->id_organizacion);
                }
            })
            ->orderByDesc('p.created_at')
            ->limit(6)
            ->get();

        foreach ($adopciones as $item) {
            $items->push((object) [
                'titulo' => $item->titulo,
                'tipo' => $item->tipo,
                'estado' => $item->estado,
                'fecha' => $item->fecha,
                'imagen' => $item->imagen,
                'url' => route('adopciones.show', $item->id_publicacion),
            ]);
        }

        if ($organizacion) {
            $consejos = DB::table('consejos as c')
                ->select(
                    'c.id_consejo',
                    'c.titulo',
                    'c.estado_publicacion as estado',
                    'c.creado_en as fecha',
                    'c.resumen as descripcion',
                    DB::raw("'Consejo' as tipo"),
                    DB::raw("(SELECT ci.url FROM consejo_imagen ci WHERE ci.consejo_id = c.id_consejo ORDER BY ci.orden ASC LIMIT 1) as imagen")
                )
                ->where('c.autor_organizacion_id', $organizacion->id_organizacion)
                ->orderByDesc('c.creado_en')
                ->limit(6)
                ->get();

            foreach ($consejos as $item) {
                $items->push((object) [
                    'titulo' => $item->titulo,
                    'tipo' => $item->tipo,
                    'estado' => $item->estado,
                    'fecha' => $item->fecha,
                    'imagen' => $item->imagen,
                    'url' => route('consejos.show', $item->id_consejo),
                ]);
            }
        }

        return $items
            ->sortByDesc('fecha')
            ->take(8)
            ->values();
    }

    private function cargarComentariosRecientes(int $usuarioId): Collection
    {
        return DB::table('comentarios_extravio as c')
            ->join('publicaciones_extravio as p', 'p.id_publicacion', '=', 'c.publicacion_id')
            ->select(
                'c.id_comentario',
                'c.comentario as texto',
                'c.creado_en as fecha',
                'p.nombre as contexto',
                'p.id_publicacion'
            )
            ->where('c.usuario_id', $usuarioId)
            ->where('c.estado', '<>', 'ELIMINADO')
            ->orderByDesc('c.creado_en')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                $item->url = route('extravios.show', $item->id_publicacion);
                return $item;
            });
    }

    private function cargarSolicitudesRecibidasRecientes(int $usuarioId, $organizacion = null): Collection
    {
        return DB::table('solicitudes_adopcion as s')
            ->join('publicaciones_adopcion as p', 'p.id_publicacion', '=', 's.publicacion_id')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 's.solicitante_usuario_id')
            ->select(
                's.id_solicitud',
                's.nombre_completo',
                's.estado',
                's.created_at as fecha',
                'p.nombre as mascota',
                'p.id_publicacion',
                'u.correo as correo_usuario'
            )
            ->where(function ($query) use ($usuarioId, $organizacion) {
                $query->where('p.autor_usuario_id', $usuarioId);

                if ($organizacion) {
                    $query->orWhere('p.autor_organizacion_id', $organizacion->id_organizacion);
                }
            })
            ->orderByDesc('s.created_at')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                $item->url = route('adopciones.show', $item->id_publicacion);
                return $item;
            });
    }

    public function perfil()
    {
        $usuario = $this->usuarioActual();
        abort_if(!$usuario, 403);

        $configuracion = $this->configuracionUsuario($usuario->id_usuario);
        $organizacion = $this->organizacionActual($usuario->id_usuario);

        $tipoPerfil = 'USUARIO';
        if ($organizacion && $organizacion->tipo === 'VETERINARIA') {
            $tipoPerfil = 'VETERINARIA';
        } elseif ($organizacion && $organizacion->tipo === 'REFUGIO') {
            $tipoPerfil = 'REFUGIO';
        }

        $conteoExtravios = $this->contarExtravios($usuario->id_usuario, $organizacion);
        $conteoAdopciones = $this->contarAdopciones($usuario->id_usuario, $organizacion);
        $conteoComentarios = $this->contarComentarios($usuario->id_usuario);
        $conteoSolicitudesEnviadas = $this->contarSolicitudesEnviadas($usuario->id_usuario);
        $conteoConsejos = $this->contarConsejos($organizacion);
        $conteoSolicitudesRecibidas = $this->contarSolicitudesRecibidas($usuario->id_usuario, $organizacion);

        $publicaciones = $this->cargarPublicacionesRecientes($usuario->id_usuario, $organizacion);
        $comentarios = $this->cargarComentariosRecientes($usuario->id_usuario);
        $solicitudesRecibidas = $this->cargarSolicitudesRecibidasRecientes($usuario->id_usuario, $organizacion);

        $panelOrganizacionUrl = null;
        $perfilPublicoUrl = null;

        if ($tipoPerfil === 'VETERINARIA') {
            $panelOrganizacionUrl = route('veterinaria.dashboard');
            $perfilPublicoUrl = route('veterinarias.show', $organizacion->id_organizacion);
        } elseif ($tipoPerfil === 'REFUGIO') {
            $panelOrganizacionUrl = route('refugio.dashboard');
            $perfilPublicoUrl = route('refugios.show', $organizacion->id_organizacion);
        }

        return view('perfil.perfil', compact(
            'usuario',
            'configuracion',
            'organizacion',
            'tipoPerfil',
            'conteoExtravios',
            'conteoAdopciones',
            'conteoComentarios',
            'conteoSolicitudesEnviadas',
            'conteoConsejos',
            'conteoSolicitudesRecibidas',
            'publicaciones',
            'comentarios',
            'solicitudesRecibidas',
            'panelOrganizacionUrl',
            'perfilPublicoUrl'
        ));
    }

    public function update(Request $request)
    {
        $usuario = $this->usuarioActual();
        abort_if(!$usuario, 403);

        $reglas = [
            'nombre' => ['required', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
        ];

        $mensajes = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 120 caracteres.',
            'telefono.max' => 'El teléfono no debe exceder 20 caracteres.',
            'whatsapp.max' => 'El WhatsApp no debe exceder 20 caracteres.',
        ];

        if (Schema::hasColumn('usuarios', 'ciudad')) {
            $reglas['ciudad'] = ['nullable', 'string', 'max:120'];
            $mensajes['ciudad.max'] = 'La ciudad no debe exceder 120 caracteres.';
        }

        $request->validate($reglas, $mensajes);

        $usuario->nombre = trim($request->nombre);
        $usuario->telefono = $request->filled('telefono') ? trim($request->telefono) : null;
        $usuario->whatsapp = $request->filled('whatsapp') ? trim($request->whatsapp) : null;

        if (Schema::hasColumn('usuarios', 'ciudad')) {
            $usuario->ciudad = $request->filled('ciudad') ? trim($request->ciudad) : null;
        }

        $usuario->save();

        return redirect()
            ->route('perfil')
            ->with('success_profile', 'Tu información fue actualizada correctamente.');
    }

    public function updatePhoto(Request $request)
    {
        return redirect()
            ->route('perfil')
            ->with('error_profile', 'La foto de perfil no está habilitada en esta versión porque esa columna no existe en la base de datos actual.');
    }

    public function updateSettings(Request $request)
    {
        $usuario = $this->usuarioActual();
        abort_if(!$usuario, 403);

        $configuracion = $this->configuracionUsuario($usuario->id_usuario);

        $configuracion->recibir_notificaciones = $request->has('recibir_notificaciones');
        $configuracion->recibir_correos = $request->has('recibir_correos');
        $configuracion->mostrar_telefono_publico = $request->has('mostrar_telefono_publico');
        $configuracion->mostrar_whatsapp_publico = $request->has('mostrar_whatsapp_publico');
        $configuracion->ocultar_ubicacion_exacta = $request->has('ocultar_ubicacion_exacta');
        $configuracion->actualizado_en = now();
        $configuracion->save();

        return redirect()
            ->route('perfil')
            ->with('success_settings', 'La configuración de tu perfil fue actualizada correctamente.');
    }
}