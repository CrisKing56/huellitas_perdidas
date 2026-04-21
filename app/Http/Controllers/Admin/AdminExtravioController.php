<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminExtravioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $estado = (string) $request->get('estado', '');

        $tieneReportes = Schema::hasTable('reportes');

        $query = DB::table('publicaciones_extravio as p')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'p.autor_usuario_id')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'p.raza_id')
            ->select(
                'p.id_publicacion',
                'p.nombre',
                'p.color',
                'p.tamano',
                'p.sexo',
                'p.fecha_extravio',
                'p.colonia_barrio',
                'p.calle_referencias',
                'p.descripcion',
                'p.estado',
                'p.created_at',
                'p.updated_at',
                'p.resuelta_en',
                'p.eliminado_en',
                'u.nombre as autor_nombre',
                'u.correo as autor_correo',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre',
                DB::raw("(SELECT ef.url FROM extravio_fotos ef WHERE ef.publicacion_id = p.id_publicacion ORDER BY ef.orden ASC LIMIT 1) as foto_principal"),
                DB::raw("(SELECT COUNT(*) FROM comentarios_extravio c WHERE c.publicacion_id = p.id_publicacion AND c.estado <> 'ELIMINADO') as total_comentarios"),
                DB::raw("(SELECT COUNT(*) FROM avistamientos_extravio a WHERE a.publicacion_id = p.id_publicacion) as total_avistamientos"),
                DB::raw($tieneReportes
                    ? "(SELECT COUNT(*) FROM reportes rep WHERE rep.objetivo_tipo = 'PUB_EXTRAVIO' AND rep.objetivo_id = p.id_publicacion) as total_reportes"
                    : "0 as total_reportes")
            );

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('p.id_publicacion', $q)
                    ->orWhere('p.nombre', 'like', "%{$q}%")
                    ->orWhere('p.colonia_barrio', 'like', "%{$q}%")
                    ->orWhere('u.nombre', 'like', "%{$q}%")
                    ->orWhere('u.correo', 'like', "%{$q}%")
                    ->orWhere('e.nombre', 'like', "%{$q}%")
                    ->orWhere('r.nombre', 'like', "%{$q}%");
            });
        }

        if ($estado !== '') {
            $query->where('p.estado', $estado);
        }

        $publicaciones = $query
            ->orderByRaw("
                CASE p.estado
                    WHEN 'ACTIVA' THEN 1
                    WHEN 'RESUELTA' THEN 2
                    WHEN 'ELIMINADA' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('p.created_at')
            ->paginate(12)
            ->withQueryString();

        $baseConteo = DB::table('publicaciones_extravio');

        $stats = [
            'total' => (clone $baseConteo)->count(),
            'activas' => (clone $baseConteo)->where('estado', 'ACTIVA')->count(),
            'resueltas' => (clone $baseConteo)->where('estado', 'RESUELTA')->count(),
            'eliminadas' => (clone $baseConteo)->where('estado', 'ELIMINADA')->count(),
            'con_reportes' => $tieneReportes
                ? DB::table('reportes')
                    ->where('objetivo_tipo', 'PUB_EXTRAVIO')
                    ->distinct('objetivo_id')
                    ->count('objetivo_id')
                : 0,
        ];

        return view('admin.extravios.index', compact('publicaciones', 'stats', 'q', 'estado'));
    }

    public function show($id)
    {
        $tieneReportes = Schema::hasTable('reportes');

        $publicacion = DB::table('publicaciones_extravio as p')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'p.autor_usuario_id')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'p.raza_id')
            ->select(
                'p.*',
                'u.nombre as autor_nombre',
                'u.correo as autor_correo',
                'u.telefono as autor_telefono',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre',
                DB::raw("(SELECT COUNT(*) FROM comentarios_extravio c WHERE c.publicacion_id = p.id_publicacion AND c.estado <> 'ELIMINADO') as total_comentarios"),
                DB::raw("(SELECT COUNT(*) FROM avistamientos_extravio a WHERE a.publicacion_id = p.id_publicacion) as total_avistamientos"),
                DB::raw($tieneReportes
                    ? "(SELECT COUNT(*) FROM reportes rep WHERE rep.objetivo_tipo = 'PUB_EXTRAVIO' AND rep.objetivo_id = p.id_publicacion) as total_reportes"
                    : "0 as total_reportes")
            )
            ->where('p.id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        $fotos = DB::table('extravio_fotos')
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->orderBy('orden', 'asc')
            ->get();

        $comentarios = DB::table('comentarios_extravio as c')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'c.usuario_id')
            ->select(
                'c.id_comentario',
                'c.publicacion_id',
                'c.usuario_id',
                'c.comentario_padre_id',
                'c.comentario',
                'c.estado',
                'c.creado_en',
                'c.actualizado_en',
                'u.nombre as autor_nombre',
                'u.correo as autor_correo'
            )
            ->where('c.publicacion_id', $publicacion->id_publicacion)
            ->orderByDesc('c.creado_en')
            ->get();

        $avistamientos = DB::table('avistamientos_extravio as a')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'a.usuario_reportante_id')
            ->select(
                'a.id_avistamiento',
                'a.publicacion_id',
                'a.usuario_reportante_id',
                'a.ubicacion_id',
                'a.nombre_contacto',
                'a.telefono_contacto',
                'a.fecha_avistamiento',
                'a.colonia_barrio',
                'a.calle_referencias',
                'a.descripcion',
                'a.foto_url',
                'a.estado',
                'a.creado_en',
                'u.nombre as reportante_nombre',
                'u.correo as reportante_correo'
            )
            ->where('a.publicacion_id', $publicacion->id_publicacion)
            ->orderByDesc('a.creado_en')
            ->get();

        $reportes = collect();

        if ($tieneReportes) {
            $reportes = DB::table('reportes as r')
                ->leftJoin('usuarios as ur', 'ur.id_usuario', '=', 'r.reportante_usuario_id')
                ->leftJoin('motivos_reporte as mr', 'mr.id_motivo', '=', 'r.motivo_id')
                ->leftJoin('usuarios as ua', 'ua.id_usuario', '=', 'r.revisado_por')
                ->select(
                    'r.id_reporte',
                    'r.objetivo_id',
                    'r.estado',
                    'r.descripcion_adicional',
                    'r.nota_resolucion',
                    'r.creado_en',
                    'r.revisado_en',
                    'ur.nombre as reportante_nombre',
                    'ur.correo as reportante_correo',
                    'mr.nombre as motivo_nombre',
                    'ua.nombre as admin_revisor_nombre'
                )
                ->where('r.objetivo_tipo', 'PUB_EXTRAVIO')
                ->where('r.objetivo_id', $publicacion->id_publicacion)
                ->orderByDesc('r.creado_en')
                ->get();
        }

        return view('admin.extravios.show', compact('publicacion', 'fotos', 'comentarios', 'avistamientos', 'reportes'));
    }

    public function ocultar($id)
    {
        $publicacion = DB::table('publicaciones_extravio')
            ->where('id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        if ($publicacion->estado === 'ELIMINADA') {
            return redirect()
                ->route('admin.extravios.show', $id)
                ->with('error', 'La publicación ya está oculta.');
        }

        DB::table('publicaciones_extravio')
            ->where('id_publicacion', $id)
            ->update([
                'estado' => 'ELIMINADA',
                'eliminado_en' => now(),
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.extravios.show', $id)
            ->with('success', 'La publicación fue ocultada correctamente.');
    }

    public function reactivar($id)
    {
        $publicacion = DB::table('publicaciones_extravio')
            ->where('id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        if ($publicacion->estado !== 'ELIMINADA') {
            return redirect()
                ->route('admin.extravios.show', $id)
                ->with('error', 'Solo se pueden reactivar publicaciones ocultas.');
        }

        DB::table('publicaciones_extravio')
            ->where('id_publicacion', $id)
            ->update([
                'estado' => 'ACTIVA',
                'eliminado_en' => null,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.extravios.show', $id)
            ->with('success', 'La publicación fue reactivada correctamente.');
    }
}