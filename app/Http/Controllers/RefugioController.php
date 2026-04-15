<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RefugioController extends Controller
{
    // Muestra el catálogo de refugios
    public function index()
    {
        $refugios = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->where('estado_revision', 'APROBADA') // Solo mostramos los aprobados
            ->orderBy('creado_en', 'desc')
            ->paginate(9);

        return view('refugios.index', compact('refugios'));
    }

    // Muestra el perfil individual de un refugio
    public function show($id)
    {
        $refugio = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->findOrFail($id);

        return view('refugios.show', compact('refugio'));
    }

    // Panel privado del refugio
    public function dashboard()
    {
        $organizacion = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->where('usuario_dueno_id', Auth::id())
            ->first();

        abort_if(!$organizacion, 403, 'No tienes un refugio asociado.');

        $tieneTablaAdopciones = Schema::hasTable('publicaciones_adopcion');
        $tieneAutorOrgEnAdopciones = $tieneTablaAdopciones && Schema::hasColumn('publicaciones_adopcion', 'autor_organizacion_id');
        $tieneEstadoEnAdopciones = $tieneTablaAdopciones && Schema::hasColumn('publicaciones_adopcion', 'estado');

        $tieneTablaConsejos = Schema::hasTable('consejos');
        $tieneAutorOrgEnConsejos = $tieneTablaConsejos && Schema::hasColumn('consejos', 'autor_organizacion_id');
        $tieneEstadoPublicacionEnConsejos = $tieneTablaConsejos && Schema::hasColumn('consejos', 'estado_publicacion');

        $stats = [
            'adopciones' => ($tieneTablaAdopciones && $tieneAutorOrgEnAdopciones)
                ? DB::table('publicaciones_adopcion')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'consejos' => ($tieneTablaConsejos && $tieneAutorOrgEnConsejos)
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->count()
                : 0,

            'mascotas_activas' => ($tieneTablaAdopciones && $tieneAutorOrgEnAdopciones && $tieneEstadoEnAdopciones)
                ? DB::table('publicaciones_adopcion')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->whereIn('estado', ['DISPONIBLE', 'EN_PROCESO', 'PAUSADA'])
                    ->count()
                : 0,

            'pendientes' => ($tieneTablaConsejos && $tieneAutorOrgEnConsejos && $tieneEstadoPublicacionEnConsejos)
                ? DB::table('consejos')
                    ->where('autor_organizacion_id', $organizacion->id_organizacion)
                    ->where('estado_publicacion', 'PENDIENTE')
                    ->count()
                : 0,
        ];

        return view('refugios.dashboard', compact('organizacion', 'stats'));
    }
}