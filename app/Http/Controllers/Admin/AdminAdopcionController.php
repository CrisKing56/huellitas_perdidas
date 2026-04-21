<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAdopcionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $estado = (string) $request->get('estado', '');

        $query = DB::table('publicaciones_adopcion as p')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'p.autor_usuario_id')
            ->leftJoin('organizaciones as o', 'o.id_organizacion', '=', 'p.autor_organizacion_id')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'p.raza_id')
            ->select(
                'p.id_publicacion',
                'p.autor_usuario_id',
                'p.autor_organizacion_id',
                'p.nombre',
                'p.especie_id',
                'p.raza_id',
                'p.otra_raza',
                'p.edad_anios',
                'p.sexo',
                'p.tamano',
                'p.color_predominante',
                'p.vacunas_aplicadas',
                'p.esterilizado',
                'p.condicion_salud',
                'p.descripcion_salud',
                'p.requisitos',
                'p.colonia_barrio',
                'p.calle_referencias',
                'p.latitud',
                'p.longitud',
                'p.descripcion',
                'p.estado',
                'p.created_at',
                'p.updated_at',
                'u.nombre as autor_nombre',
                'u.correo as autor_correo',
                'o.nombre as organizacion_nombre',
                'o.tipo as organizacion_tipo',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre',
                DB::raw("(SELECT af.url FROM adopcion_fotos af WHERE af.publicacion_id = p.id_publicacion ORDER BY af.orden ASC LIMIT 1) as foto_principal"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion) as total_solicitudes"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'ENVIADA') as solicitudes_enviadas"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'ACEPTADA') as solicitudes_aceptadas"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'RECHAZADA') as solicitudes_rechazadas")
            );

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('p.id_publicacion', $q)
                    ->orWhere('p.nombre', 'like', "%{$q}%")
                    ->orWhere('p.colonia_barrio', 'like', "%{$q}%")
                    ->orWhere('u.nombre', 'like', "%{$q}%")
                    ->orWhere('u.correo', 'like', "%{$q}%")
                    ->orWhere('o.nombre', 'like', "%{$q}%")
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
                    WHEN 'DISPONIBLE' THEN 1
                    WHEN 'EN_PROCESO' THEN 2
                    WHEN 'PAUSADA' THEN 3
                    WHEN 'ADOPTADA' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('p.created_at')
            ->paginate(12)
            ->withQueryString();

        $baseConteo = DB::table('publicaciones_adopcion');

        $stats = [
            'total' => (clone $baseConteo)->count(),
            'disponibles' => (clone $baseConteo)->where('estado', 'DISPONIBLE')->count(),
            'en_proceso' => (clone $baseConteo)->where('estado', 'EN_PROCESO')->count(),
            'pausadas' => (clone $baseConteo)->where('estado', 'PAUSADA')->count(),
            'adoptadas' => (clone $baseConteo)->where('estado', 'ADOPTADA')->count(),
        ];

        return view('admin.adopciones.index', compact('publicaciones', 'stats', 'q', 'estado'));
    }

    public function show($id)
    {
        $publicacion = DB::table('publicaciones_adopcion as p')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'p.autor_usuario_id')
            ->leftJoin('organizaciones as o', 'o.id_organizacion', '=', 'p.autor_organizacion_id')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'p.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'p.raza_id')
            ->select(
                'p.*',
                'u.nombre as autor_nombre',
                'u.correo as autor_correo',
                'u.telefono as autor_telefono',
                'o.nombre as organizacion_nombre',
                'o.tipo as organizacion_tipo',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre',
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion) as total_solicitudes"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'ENVIADA') as solicitudes_enviadas"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'ACEPTADA') as solicitudes_aceptadas"),
                DB::raw("(SELECT COUNT(*) FROM solicitudes_adopcion s WHERE s.publicacion_id = p.id_publicacion AND s.estado = 'RECHAZADA') as solicitudes_rechazadas")
            )
            ->where('p.id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        $fotos = DB::table('adopcion_fotos')
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->orderBy('orden', 'asc')
            ->get();

        $solicitudes = DB::table('solicitudes_adopcion as s')
            ->leftJoin('usuarios as u', 'u.id_usuario', '=', 's.solicitante_usuario_id')
            ->select(
                's.id_solicitud',
                's.publicacion_id',
                's.solicitante_usuario_id',
                's.nombre_completo',
                's.edad',
                's.estado_civil',
                's.tipo_vivienda',
                's.tiene_patio',
                's.todos_de_acuerdo',
                's.motivo_adopcion',
                's.estado',
                's.created_at',
                's.updated_at',
                'u.nombre as usuario_nombre',
                'u.correo as usuario_correo',
                'u.telefono as usuario_telefono'
            )
            ->where('s.publicacion_id', $publicacion->id_publicacion)
            ->orderByRaw("
                CASE s.estado
                    WHEN 'ACEPTADA' THEN 1
                    WHEN 'ENVIADA' THEN 2
                    WHEN 'RECHAZADA' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('s.created_at')
            ->get();

        return view('admin.adopciones.show', compact('publicacion', 'fotos', 'solicitudes'));
    }

    public function pausar($id)
    {
        $publicacion = DB::table('publicaciones_adopcion')
            ->where('id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        if ($publicacion->estado === 'PAUSADA') {
            return redirect()
                ->route('admin.adopciones.show', $id)
                ->with('error', 'La publicación ya está pausada.');
        }

        if ($publicacion->estado === 'ADOPTADA') {
            return redirect()
                ->route('admin.adopciones.show', $id)
                ->with('error', 'No se puede pausar una publicación ya marcada como adoptada.');
        }

        DB::table('publicaciones_adopcion')
            ->where('id_publicacion', $id)
            ->update([
                'estado' => 'PAUSADA',
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.adopciones.show', $id)
            ->with('success', 'La publicación fue pausada correctamente.');
    }

    public function reactivar($id)
    {
        $publicacion = DB::table('publicaciones_adopcion')
            ->where('id_publicacion', $id)
            ->first();

        abort_if(!$publicacion, 404);

        if ($publicacion->estado !== 'PAUSADA') {
            return redirect()
                ->route('admin.adopciones.show', $id)
                ->with('error', 'Solo se pueden reactivar publicaciones pausadas.');
        }

        DB::table('publicaciones_adopcion')
            ->where('id_publicacion', $id)
            ->update([
                'estado' => 'DISPONIBLE',
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.adopciones.show', $id)
            ->with('success', 'La publicación fue reactivada correctamente.');
    }
}