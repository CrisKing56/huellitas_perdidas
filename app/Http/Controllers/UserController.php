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

    private function valorUsuario($usuario, array $campos, $default = null)
    {
        foreach ($campos as $campo) {
            $valor = data_get($usuario, $campo);

            if ($valor !== null && $valor !== '') {
                return $valor;
            }
        }

        return $default;
    }

    private function esAdmin($usuario): bool
    {
        if (!$usuario) {
            return false;
        }

        $rol = strtoupper((string) $this->valorUsuario(
            $usuario,
            ['rol', 'role', 'tipo_usuario', 'tipo', 'perfil'],
            ''
        ));

        if (in_array($rol, ['ADMIN', 'ADMINISTRADOR', 'ADMIN_GENERAL'], true)) {
            return true;
        }

        $bandera = $this->valorUsuario($usuario, ['es_admin', 'is_admin'], 0);

        return in_array($bandera, [1, '1', true, 'true', 'TRUE'], true);
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
        if (!Schema::hasTable('organizaciones')) {
            return null;
        }

        $query = DB::table('organizaciones as o');

        if (Schema::hasTable('direcciones')) {
            $query->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
                ->select(
                    'o.*',
                    'd.calle_numero',
                    'd.colonia',
                    'd.codigo_postal',
                    'd.ciudad as ciudad_direccion',
                    'd.estado as estado_direccion'
                );
        } else {
            $query->select('o.*');
        }

        return $query
            ->where('o.usuario_dueno_id', $usuarioId)
            ->first();
    }

    private function contarTabla(string $tabla): int
    {
        return Schema::hasTable($tabla) ? DB::table($tabla)->count() : 0;
    }

    private function contarOrganizacionesPorTipo(string $tipo): int
    {
        if (!Schema::hasTable('organizaciones')) {
            return 0;
        }

        return DB::table('organizaciones')
            ->where('tipo', $tipo)
            ->count();
    }

    private function contarReportesAdmin(): int
    {
        $total = 0;

        if (Schema::hasTable('reportes')) {
            $total += DB::table('reportes')->count();
        }

        if (Schema::hasTable('reportes_consejos')) {
            $total += DB::table('reportes_consejos')->count();
        }

        return $total;
    }

    private function contarPendientesAdmin(): int
    {
        $total = 0;

        if (Schema::hasTable('publicaciones_extravio') && Schema::hasColumn('publicaciones_extravio', 'estado')) {
            $total += DB::table('publicaciones_extravio')
                ->whereIn('estado', ['PENDIENTE', 'EN_REVISION'])
                ->count();
        }

        if (Schema::hasTable('publicaciones_adopcion') && Schema::hasColumn('publicaciones_adopcion', 'estado')) {
            $total += DB::table('publicaciones_adopcion')
                ->whereIn('estado', ['PENDIENTE', 'EN_REVISION'])
                ->count();
        }

        if (Schema::hasTable('consejos') && Schema::hasColumn('consejos', 'estado_publicacion')) {
            $total += DB::table('consejos')
                ->whereIn('estado_publicacion', ['PENDIENTE', 'EN_REVISION'])
                ->count();
        }

        return $total;
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
        if (!Schema::hasTable('comentarios_extravio')) {
            return 0;
        }

        $query = DB::table('comentarios_extravio')
            ->where('usuario_id', $usuarioId);

        if (Schema::hasColumn('comentarios_extravio', 'estado')) {
            $query->where('estado', '<>', 'ELIMINADO');
        }

        return $query->count();
    }

    private function contarSolicitudesEnviadas(int $usuarioId): int
    {
        return SolicitudAdopcion::where('solicitante_usuario_id', $usuarioId)->count();
    }

    private function contarConsejos($organizacion): int
    {
        if (!$organizacion || !Schema::hasTable('consejos')) {
            return 0;
        }

        return DB::table('consejos')
            ->where('autor_organizacion_id', $organizacion->id_organizacion)
            ->count();
    }

    private function contarSolicitudesRecibidas(int $usuarioId, $organizacion = null): int
    {
        if (!Schema::hasTable('solicitudes_adopcion') || !Schema::hasTable('publicaciones_adopcion')) {
            return 0;
        }

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

        if (Schema::hasTable('publicaciones_extravio')) {
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
        }

        if (Schema::hasTable('publicaciones_adopcion')) {
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
        }

        if ($organizacion && Schema::hasTable('consejos')) {
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
        if (!Schema::hasTable('comentarios_extravio') || !Schema::hasTable('publicaciones_extravio')) {
            return collect();
        }

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
            ->when(Schema::hasColumn('comentarios_extravio', 'estado'), function ($query) {
                $query->where('c.estado', '<>', 'ELIMINADO');
            })
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
        if (!Schema::hasTable('solicitudes_adopcion') || !Schema::hasTable('publicaciones_adopcion')) {
            return collect();
        }

        $query = DB::table('solicitudes_adopcion as s')
            ->join('publicaciones_adopcion as p', 'p.id_publicacion', '=', 's.publicacion_id');

        if (Schema::hasTable('usuarios')) {
            $query->leftJoin('usuarios as u', 'u.id_usuario', '=', 's.solicitante_usuario_id');
        }

        return $query
            ->select(
                's.id_solicitud',
                's.nombre_completo',
                's.estado',
                's.created_at as fecha',
                'p.nombre as mascota',
                'p.id_publicacion',
                DB::raw(Schema::hasTable('usuarios') ? 'u.correo as correo_usuario' : 'NULL as correo_usuario')
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
        $adminResumen = [
            'usuarios' => 0,
            'organizaciones' => 0,
            'reportes' => 0,
            'pendientes' => 0,
            'veterinarias' => 0,
            'refugios' => 0,
        ];

        if ($this->esAdmin($usuario)) {
            $tipoPerfil = 'ADMIN';
        } elseif ($organizacion && strtoupper((string) $organizacion->tipo) === 'VETERINARIA') {
            $tipoPerfil = 'VETERINARIA';
        } elseif ($organizacion && strtoupper((string) $organizacion->tipo) === 'REFUGIO') {
            $tipoPerfil = 'REFUGIO';
        }

        $panelOrganizacionUrl = null;
        $perfilPublicoUrl = null;

        if ($tipoPerfil === 'ADMIN') {
            $conteoExtravios = $this->contarTabla('publicaciones_extravio');
            $conteoAdopciones = $this->contarTabla('publicaciones_adopcion');
            $conteoComentarios = $this->contarTabla('comentarios_extravio');
            $conteoSolicitudesEnviadas = 0;
            $conteoConsejos = $this->contarTabla('consejos');
            $conteoSolicitudesRecibidas = $this->contarTabla('solicitudes_adopcion');

            $publicaciones = collect();
            $comentarios = collect();
            $solicitudesRecibidas = collect();

            $panelOrganizacionUrl = route('admin.dashboard');

            $adminResumen = [
                'usuarios' => $this->contarTabla('usuarios'),
                'organizaciones' => $this->contarTabla('organizaciones'),
                'reportes' => $this->contarReportesAdmin(),
                'pendientes' => $this->contarPendientesAdmin(),
                'veterinarias' => $this->contarOrganizacionesPorTipo('VETERINARIA'),
                'refugios' => $this->contarOrganizacionesPorTipo('REFUGIO'),
            ];
        } else {
            $conteoExtravios = $this->contarExtravios($usuario->id_usuario, $organizacion);
            $conteoAdopciones = $this->contarAdopciones($usuario->id_usuario, $organizacion);
            $conteoComentarios = $this->contarComentarios($usuario->id_usuario);
            $conteoSolicitudesEnviadas = $this->contarSolicitudesEnviadas($usuario->id_usuario);
            $conteoConsejos = $this->contarConsejos($organizacion);
            $conteoSolicitudesRecibidas = $this->contarSolicitudesRecibidas($usuario->id_usuario, $organizacion);

            $publicaciones = $this->cargarPublicacionesRecientes($usuario->id_usuario, $organizacion);
            $comentarios = $this->cargarComentariosRecientes($usuario->id_usuario);
            $solicitudesRecibidas = $this->cargarSolicitudesRecibidasRecientes($usuario->id_usuario, $organizacion);

            if ($tipoPerfil === 'VETERINARIA' && $organizacion) {
                $panelOrganizacionUrl = route('veterinaria.dashboard');
                $perfilPublicoUrl = route('veterinarias.show', $organizacion->id_organizacion);
            } elseif ($tipoPerfil === 'REFUGIO' && $organizacion) {
                $panelOrganizacionUrl = route('refugio.dashboard');
                $perfilPublicoUrl = route('refugios.show', $organizacion->id_organizacion);
            }
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
            'perfilPublicoUrl',
            'adminResumen'
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