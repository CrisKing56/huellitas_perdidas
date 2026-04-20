<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('es');

        $stats = $this->buildStats();

        [$chartData, $maxChart] = $this->buildMonthlyChart();

        $estadoResumen = $this->buildStateSummary();

        $actividad = $this->buildRecentActivity();

        $quickActions = $this->buildQuickActions();

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'maxChart',
            'estadoResumen',
            'actividad',
            'quickActions'
        ));
    }

    private function buildStats(): array
    {
        $usuarios = Schema::hasTable('usuarios')
            ? DB::table('usuarios')->count()
            : 0;

        $extraviosActivos = Schema::hasTable('publicaciones_extravio')
            ? DB::table('publicaciones_extravio')->where('estado', 'ACTIVA')->count()
            : 0;

        $adopcionesActivas = Schema::hasTable('publicaciones_adopcion')
            ? DB::table('publicaciones_adopcion')
                ->whereIn('estado', ['DISPONIBLE', 'EN_PROCESO'])
                ->count()
            : 0;

        $consejosAprobados = Schema::hasTable('consejos')
            ? DB::table('consejos')->where('estado_publicacion', 'APROBADO')->count()
            : 0;

        $reportesPendientes = 0;

        if (Schema::hasTable('reportes')) {
            $reportesPendientes += DB::table('reportes')
                ->whereIn('estado', ['ENVIADO', 'EN_REVISION'])
                ->count();
        }

        if (Schema::hasTable('reportes_consejo')) {
            $reportesPendientes += DB::table('reportes_consejo')
                ->whereIn('estado', ['ABIERTO', 'EN_REVISION'])
                ->count();
        }

        $comentariosRegistrados = Schema::hasTable('comentarios_extravio')
            ? DB::table('comentarios_extravio')
                ->where('estado', '<>', 'ELIMINADO')
                ->count()
            : 0;

        $veterinarias = Schema::hasTable('organizaciones')
            ? DB::table('organizaciones')
                ->where('tipo', 'VETERINARIA')
                ->where('estado_revision', 'APROBADA')
                ->count()
            : 0;

        $refugios = Schema::hasTable('organizaciones')
            ? DB::table('organizaciones')
                ->where('tipo', 'REFUGIO')
                ->where('estado_revision', 'APROBADA')
                ->count()
            : 0;

        return [
            ['titulo' => 'Usuarios registrados', 'valor' => number_format($usuarios), 'icono' => 'users', 'color' => 'bg-blue-50 text-blue-600'],
            ['titulo' => 'Mascotas extraviadas activas', 'valor' => number_format($extraviosActivos), 'icono' => 'search', 'color' => 'bg-orange-50 text-orange-500'],
            ['titulo' => 'Adopciones activas', 'valor' => number_format($adopcionesActivas), 'icono' => 'heart', 'color' => 'bg-green-50 text-green-600'],
            ['titulo' => 'Consejos aprobados', 'valor' => number_format($consejosAprobados), 'icono' => 'book', 'color' => 'bg-indigo-50 text-indigo-600'],
            ['titulo' => 'Reportes pendientes', 'valor' => number_format($reportesPendientes), 'icono' => 'alert', 'color' => 'bg-red-50 text-red-500'],
            ['titulo' => 'Comentarios registrados', 'valor' => number_format($comentariosRegistrados), 'icono' => 'comment', 'color' => 'bg-yellow-50 text-yellow-600'],
            ['titulo' => 'Veterinarias aprobadas', 'valor' => number_format($veterinarias), 'icono' => 'building', 'color' => 'bg-purple-50 text-purple-600'],
            ['titulo' => 'Refugios aprobados', 'valor' => number_format($refugios), 'icono' => 'home', 'color' => 'bg-emerald-50 text-emerald-600'],
        ];
    }

    private function buildMonthlyChart(): array
    {
        $chartData = [];
        $maxChart = 1;

        for ($i = 5; $i >= 0; $i--) {
            $inicio = now()->startOfMonth()->subMonths($i);
            $fin = (clone $inicio)->endOfMonth();

            $extra = Schema::hasTable('publicaciones_extravio')
                ? DB::table('publicaciones_extravio')
                    ->whereBetween('created_at', [$inicio, $fin])
                    ->count()
                : 0;

            $adop = Schema::hasTable('publicaciones_adopcion')
                ? DB::table('publicaciones_adopcion')
                    ->whereBetween('created_at', [$inicio, $fin])
                    ->count()
                : 0;

            $chartData[] = [
                'mes' => ucfirst($inicio->copy()->locale('es')->translatedFormat('M')),
                'extra' => $extra,
                'adop' => $adop,
            ];

            $maxChart = max($maxChart, $extra, $adop);
        }

        return [$chartData, $maxChart];
    }

    private function buildStateSummary(): array
    {
        $activas = 0;
        $pendientes = 0;
        $finalizadas = 0;

        if (Schema::hasTable('publicaciones_extravio')) {
            $activas += DB::table('publicaciones_extravio')->where('estado', 'ACTIVA')->count();
            $finalizadas += DB::table('publicaciones_extravio')->whereIn('estado', ['RESUELTA', 'ELIMINADA'])->count();
        }

        if (Schema::hasTable('publicaciones_adopcion')) {
            $activas += DB::table('publicaciones_adopcion')->whereIn('estado', ['DISPONIBLE', 'EN_PROCESO'])->count();
            $finalizadas += DB::table('publicaciones_adopcion')->whereIn('estado', ['ADOPTADA', 'PAUSADA'])->count();
        }

        if (Schema::hasTable('consejos')) {
            $activas += DB::table('consejos')->where('estado_publicacion', 'APROBADO')->count();
            $pendientes += DB::table('consejos')->where('estado_publicacion', 'PENDIENTE')->count();
            $finalizadas += DB::table('consejos')->where('estado_publicacion', 'RECHAZADO')->count();
        }

        $total = $activas + $pendientes + $finalizadas;
        $base = $total > 0 ? $total : 1;

        return [
            'activas' => $activas,
            'pendientes' => $pendientes,
            'finalizadas' => $finalizadas,
            'activas_pct' => round(($activas / $base) * 100, 1),
            'pendientes_pct' => round(($pendientes / $base) * 100, 1),
            'finalizadas_pct' => round(($finalizadas / $base) * 100, 1),
        ];
    }

    private function buildRecentActivity(): Collection
    {
        $actividad = collect();

        if (Schema::hasTable('publicaciones_extravio')) {
            $extravios = DB::table('publicaciones_extravio as p')
                ->leftJoin('extravio_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                        ->whereRaw('f.orden = (SELECT MIN(f2.orden) FROM extravio_fotos f2 WHERE f2.publicacion_id = p.id_publicacion)');
                })
                ->select(
                    'p.id_publicacion',
                    'p.nombre',
                    'p.colonia_barrio',
                    'p.estado',
                    'p.created_at',
                    'f.url as foto_url'
                )
                ->orderByDesc('p.created_at')
                ->limit(4)
                ->get();

            foreach ($extravios as $item) {
                $timestamp = $item->created_at ? Carbon::parse($item->created_at) : now();

                $actividad->push([
                    'titulo' => $item->nombre . ' - Mascota extraviada',
                    'sub' => 'Publicado en ' . ($item->colonia_barrio ?: 'zona no especificada'),
                    'time' => $timestamp->diffForHumans(),
                    'estado' => $this->traducirEstadoExtravio($item->estado),
                    'estadoColor' => $this->colorEstadoExtravio($item->estado),
                    'img' => $item->foto_url ? asset('storage/' . $item->foto_url) : null,
                    'url' => Route::has('admin.extravios.show') ? route('admin.extravios.show', $item->id_publicacion) : null,
                    'timestamp' => $timestamp,
                ]);
            }
        }

        if (Schema::hasTable('publicaciones_adopcion')) {
            $adopciones = DB::table('publicaciones_adopcion as p')
                ->leftJoin('adopcion_fotos as f', function ($join) {
                    $join->on('f.publicacion_id', '=', 'p.id_publicacion')
                        ->whereRaw('f.orden = (SELECT MIN(f2.orden) FROM adopcion_fotos f2 WHERE f2.publicacion_id = p.id_publicacion)');
                })
                ->select(
                    'p.id_publicacion',
                    'p.nombre',
                    'p.edad_anios',
                    'p.estado',
                    'p.created_at',
                    'f.url as foto_url'
                )
                ->orderByDesc('p.created_at')
                ->limit(4)
                ->get();

            foreach ($adopciones as $item) {
                $timestamp = $item->created_at ? Carbon::parse($item->created_at) : now();

                $actividad->push([
                    'titulo' => $item->nombre . ' - Mascota en adopción',
                    'sub' => 'Edad aproximada: ' . ($item->edad_anios ?? 'No especificada') . ' años',
                    'time' => $timestamp->diffForHumans(),
                    'estado' => $this->traducirEstadoAdopcion($item->estado),
                    'estadoColor' => $this->colorEstadoAdopcion($item->estado),
                    'img' => $item->foto_url ? asset('storage/' . $item->foto_url) : null,
                    'url' => Route::has('admin.adopciones.show') ? route('admin.adopciones.show', $item->id_publicacion) : null,
                    'timestamp' => $timestamp,
                ]);
            }
        }

        if (Schema::hasTable('solicitudes_adopcion')) {
            $solicitudes = DB::table('solicitudes_adopcion as s')
                ->leftJoin('publicaciones_adopcion as p', 'p.id_publicacion', '=', 's.publicacion_id')
                ->select(
                    's.id_solicitud',
                    's.publicacion_id',
                    's.nombre_completo',
                    's.estado',
                    's.created_at',
                    'p.nombre as mascota_nombre'
                )
                ->orderByDesc('s.created_at')
                ->limit(4)
                ->get();

            foreach ($solicitudes as $item) {
                $timestamp = $item->created_at ? Carbon::parse($item->created_at) : now();

                $actividad->push([
                    'titulo' => 'Solicitud de adopción',
                    'sub' => ($item->nombre_completo ?: 'Usuario') . ' solicitó adoptar a ' . ($item->mascota_nombre ?: 'una mascota'),
                    'time' => $timestamp->diffForHumans(),
                    'estado' => $this->traducirEstadoSolicitud($item->estado),
                    'estadoColor' => $this->colorEstadoSolicitud($item->estado),
                    'img' => null,
                    'url' => Route::has('admin.adopciones.show') && $item->publicacion_id
                        ? route('admin.adopciones.show', $item->publicacion_id)
                        : null,
                    'timestamp' => $timestamp,
                ]);
            }
        }

        if (Schema::hasTable('consejos')) {
            $consejos = DB::table('consejos as c')
                ->leftJoin('organizaciones as o', 'o.id_organizacion', '=', 'c.autor_organizacion_id')
                ->leftJoin('consejo_imagen as i', function ($join) {
                    $join->on('i.consejo_id', '=', 'c.id_consejo')
                        ->whereRaw('i.orden = (SELECT MIN(i2.orden) FROM consejo_imagen i2 WHERE i2.consejo_id = c.id_consejo)');
                })
                ->select(
                    'c.id_consejo',
                    'c.titulo',
                    'c.estado_publicacion',
                    'c.creado_en',
                    'o.nombre as organizacion_nombre',
                    'i.url as foto_url'
                )
                ->orderByDesc('c.creado_en')
                ->limit(4)
                ->get();

            foreach ($consejos as $item) {
                $timestamp = $item->creado_en ? Carbon::parse($item->creado_en) : now();

                $actividad->push([
                    'titulo' => $item->titulo,
                    'sub' => 'Consejo publicado por ' . ($item->organizacion_nombre ?: 'organización'),
                    'time' => $timestamp->diffForHumans(),
                    'estado' => $this->traducirEstadoConsejo($item->estado_publicacion),
                    'estadoColor' => $this->colorEstadoConsejo($item->estado_publicacion),
                    'img' => $item->foto_url ? asset('storage/' . $item->foto_url) : null,
                    'url' => Route::has('admin.consejos.show') ? route('admin.consejos.show', $item->id_consejo) : null,
                    'timestamp' => $timestamp,
                ]);
            }
        }

        return $actividad
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();
    }

    private function buildQuickActions(): array
    {
        return [
            [
                'texto' => 'Ver publicaciones de extravío',
                'ruta' => Route::has('admin.extravios.index') ? route('admin.extravios.index') : '#',
                'color' => 'bg-orange-50 text-orange-600 border-orange-100',
            ],
            [
                'texto' => 'Ver publicaciones de adopción',
                'ruta' => Route::has('admin.adopciones.index') ? route('admin.adopciones.index') : '#',
                'color' => 'bg-green-50 text-green-600 border-green-100',
            ],
            [
                'texto' => 'Revisar consejos',
                'ruta' => Route::has('admin.consejos.index') ? route('admin.consejos.index') : '#',
                'color' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            ],
            [
                'texto' => 'Revisar reportes de publicaciones',
                'ruta' => Route::has('admin.reportes.index') ? route('admin.reportes.index') : '#',
                'color' => 'bg-red-50 text-red-600 border-red-100',
            ],
            [
                'texto' => 'Revisar reportes de consejos',
                'ruta' => Route::has('admin.reportes-consejos.index') ? route('admin.reportes-consejos.index') : '#',
                'color' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            ],
            [
                'texto' => 'Ver veterinarias registradas',
                'ruta' => Route::has('admin.veterinarias.index') ? route('admin.veterinarias.index') : '#',
                'color' => 'bg-purple-50 text-purple-600 border-purple-100',
            ],
        ];
    }

    private function traducirEstadoExtravio(?string $estado): string
    {
        return match ($estado) {
            'ACTIVA' => 'Activa',
            'RESUELTA' => 'Resuelta',
            'ELIMINADA' => 'Oculta',
            default => 'Sin estado',
        };
    }

    private function colorEstadoExtravio(?string $estado): string
    {
        return match ($estado) {
            'ACTIVA' => 'bg-yellow-100 text-yellow-700',
            'RESUELTA' => 'bg-green-100 text-green-700',
            'ELIMINADA' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function traducirEstadoAdopcion(?string $estado): string
    {
        return match ($estado) {
            'DISPONIBLE' => 'Disponible',
            'EN_PROCESO' => 'En proceso',
            'PAUSADA' => 'Pausada',
            'ADOPTADA' => 'Adoptada',
            default => 'Sin estado',
        };
    }

    private function colorEstadoAdopcion(?string $estado): string
    {
        return match ($estado) {
            'DISPONIBLE' => 'bg-green-100 text-green-700',
            'EN_PROCESO' => 'bg-yellow-100 text-yellow-700',
            'PAUSADA' => 'bg-red-100 text-red-700',
            'ADOPTADA' => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function traducirEstadoSolicitud(?string $estado): string
    {
        return match ($estado) {
            'ENVIADA' => 'Enviada',
            'ACEPTADA' => 'Aceptada',
            'RECHAZADA' => 'Rechazada',
            'CANCELADA' => 'Cancelada',
            default => 'Sin estado',
        };
    }

    private function colorEstadoSolicitud(?string $estado): string
    {
        return match ($estado) {
            'ENVIADA' => 'bg-yellow-100 text-yellow-700',
            'ACEPTADA' => 'bg-green-100 text-green-700',
            'RECHAZADA' => 'bg-red-100 text-red-700',
            'CANCELADA' => 'bg-gray-200 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function traducirEstadoConsejo(?string $estado): string
    {
        return match ($estado) {
            'PENDIENTE' => 'Pendiente',
            'APROBADO' => 'Aprobado',
            'RECHAZADO' => 'Rechazado',
            default => 'Sin estado',
        };
    }

    private function colorEstadoConsejo(?string $estado): string
    {
        return match ($estado) {
            'PENDIENTE' => 'bg-yellow-100 text-yellow-700',
            'APROBADO' => 'bg-green-100 text-green-700',
            'RECHAZADO' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}