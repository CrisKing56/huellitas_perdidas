<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ubicacion;
use App\Models\PublicacionExtravio;
use App\Models\ExtravioFoto;
use Illuminate\Http\Request;

class ExtravioController extends Controller
{
    public function create()
    {
        return view('reportar-mascota');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required',
            'foto' => 'required|image|max:5120',
            'colonia_barrio' => 'required',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric'
        ]);

        try {
            DB::transaction(function () use ($request) {

                $nuevaUbicacion = Ubicacion::create([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'precision_metros' => null
                ]);

                $publicacion = PublicacionExtravio::create([
                    'autor_usuario_id' => Auth::id(),
                    'ubicacion_id' => $nuevaUbicacion->id_ubicacion,
                    'nombre' => $request->nombre,
                    'especie_id' => $request->especie_id,
                    'color' => $request->color,
                    'tamano' => $request->tamano,
                    'sexo' => $request->sexo,
                    'fecha_extravio' => $request->fecha_extravio,
                    'colonia_barrio' => $request->colonia_barrio,
                    'descripcion' => $request->descripcion,
                    'calle_referencias' => $request->calle_referencias,
                    'estado' => 'ACTIVA'
                ]);

                if ($request->hasFile('foto')) {
                    $rutaFoto = $request->file('foto')->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacion->id_publicacion,
                        'url' => $rutaFoto,
                        'orden' => 1
                    ]);
                }
            });

            return redirect()->route('mascotas.index2')->with('success', '¡Reporte guardado exitosamente!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Hubo un problema al guardar el reporte: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $mascotas = PublicacionExtravio::where('autor_usuario_id', Auth::id())
            ->with('fotos')
            ->orderBy('id_publicacion', 'desc')
            ->paginate(10);

        return view('extravios.mis-reportes', compact('mascotas'));
    }

    public function index2(Request $request)
    {
        $filtros = [
            'q' => trim((string) $request->get('q', '')),
            'estado' => (string) $request->get('estado', 'ACTIVA'),
            'especie' => (string) $request->get('especie', ''),
            'raza' => (string) $request->get('raza', ''),
            'sexo' => (string) $request->get('sexo', ''),
            'tamano' => (string) $request->get('tamano', ''),
            'colonia' => trim((string) $request->get('colonia', '')),
            'orden' => (string) $request->get('orden', 'recientes'),
        ];

        $query = PublicacionExtravio::query()
            ->with('fotoPrincipal')
            ->leftJoin('especies as e', 'e.id_especie', '=', 'publicaciones_extravio.especie_id')
            ->leftJoin('razas as r', 'r.id_raza', '=', 'publicaciones_extravio.raza_id')
            ->select(
                'publicaciones_extravio.*',
                'e.nombre as especie_nombre',
                'r.nombre as raza_nombre'
            )
            ->where('publicaciones_extravio.estado', '!=', 'ELIMINADA')
            ->when($filtros['q'] !== '', function ($q) use ($filtros) {
                $q->where(function ($sub) use ($filtros) {
                    $sub->where('publicaciones_extravio.nombre', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('publicaciones_extravio.descripcion', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('publicaciones_extravio.colonia_barrio', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('publicaciones_extravio.otra_raza', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('e.nombre', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('r.nombre', 'like', '%' . $filtros['q'] . '%');
                });
            })
            ->when($filtros['estado'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.estado', $filtros['estado']);
            })
            ->when($filtros['especie'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.especie_id', $filtros['especie']);
            })
            ->when($filtros['raza'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.raza_id', $filtros['raza']);
            })
            ->when($filtros['sexo'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.sexo', $filtros['sexo']);
            })
            ->when($filtros['tamano'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.tamano', $filtros['tamano']);
            })
            ->when($filtros['colonia'] !== '', function ($q) use ($filtros) {
                $q->where('publicaciones_extravio.colonia_barrio', 'like', '%' . $filtros['colonia'] . '%');
            });

        switch ($filtros['orden']) {
            case 'antiguos':
                $query->orderBy('publicaciones_extravio.fecha_extravio', 'asc')
                    ->orderBy('publicaciones_extravio.id_publicacion', 'asc');
                break;

            case 'nombre_az':
                $query->orderBy('publicaciones_extravio.nombre', 'asc');
                break;

            case 'nombre_za':
                $query->orderBy('publicaciones_extravio.nombre', 'desc');
                break;

            default:
                $query->orderBy('publicaciones_extravio.fecha_extravio', 'desc')
                    ->orderBy('publicaciones_extravio.id_publicacion', 'desc');
                break;
        }

        $mascotas = $query->paginate(12)->withQueryString();

        $especies = DB::table('especies')
            ->orderBy('nombre', 'asc')
            ->get();

        $razas = collect();

        if ($filtros['especie'] !== '') {
            $razas = DB::table('razas')
                ->where('especie_id', $filtros['especie'])
                ->orderBy('nombre', 'asc')
                ->get();
        }

        $conteos = [
            'todas' => PublicacionExtravio::where('estado', '!=', 'ELIMINADA')->count(),
            'activas' => PublicacionExtravio::where('estado', 'ACTIVA')->count(),
            'resueltas' => PublicacionExtravio::where('estado', 'RESUELTA')->count(),
        ];

        return view('mascotas-perdidas', compact(
            'mascotas',
            'filtros',
            'conteos',
            'especies',
            'razas'
        ));
    }
    public function destroy($id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        if (Auth::id() != $publicacion->autor_usuario_id) {
            return back()->with('error', 'No tienes permiso para borrar esto.');
        }

        DB::table('extravio_fotos')->where('publicacion_id', $id)->delete();
        $publicacion->delete();

        return back()->with('success', 'Reporte eliminado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        if (Auth::id() != $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No puedes editar esto.');
        }

        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required',
            'colonia_barrio' => 'required',
            'foto' => 'nullable|image|max:5120',
        ]);

        $publicacion->update([
            'nombre' => $request->nombre,
            'especie_id' => $request->especie_id,
            'color' => $request->color,
            'tamano' => $request->tamano,
            'sexo' => $request->sexo,
            'fecha_extravio' => $request->fecha_extravio,
            'colonia_barrio' => $request->colonia_barrio,
            'descripcion' => $request->descripcion,
            'calle_referencias' => $request->calle_referencias,
        ]);

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('mascotas', 'public');

            DB::table('extravio_fotos')
                ->updateOrInsert(
                    ['publicacion_id' => $publicacion->id_publicacion],
                    ['url' => $rutaFoto, 'orden' => 1]
                );
        }

        return redirect()->route('extravios.index')->with('success', '¡Mascota actualizada!');
    }

    public function edit($id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        if (Auth::id() != $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No tienes permiso para editar esta publicación.');
        }

        return view('extravios.editar', compact('publicacion'));
    }

    public function show($id)
    {
        $publicacion = PublicacionExtravio::with(['autor', 'fotoPrincipal', 'ubicacion'])->findOrFail($id);

        $comentariosTodos = DB::table('comentarios_extravio as c')
            ->join('usuarios as u', 'u.id_usuario', '=', 'c.usuario_id')
            ->where('c.publicacion_id', $id)
            ->whereIn('c.estado', ['VISIBLE', 'ELIMINADO'])
            ->select(
                'c.id_comentario',
                'c.usuario_id',
                'c.comentario_padre_id',
                'c.comentario',
                'c.estado',
                'c.creado_en',
                'c.actualizado_en',
                'u.nombre as usuario_nombre'
            )
            ->orderBy('c.creado_en', 'asc')
            ->get();

        $comentarios = $comentariosTodos->whereNull('comentario_padre_id')->values();
        $respuestasPorPadre = $comentariosTodos->whereNotNull('comentario_padre_id')->groupBy('comentario_padre_id');

        $motivosReporte = DB::table('motivos_reporte')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('mascota-detalle', compact(
            'publicacion',
            'comentarios',
            'respuestasPorPadre',
            'motivosReporte'
        ));
    }

    public function storeComment(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        $request->validate([
            'comentario' => 'required|string|max:1000',
            'comentario_padre_id' => 'nullable|integer'
        ]);

        $comentarioPadreId = $request->input('comentario_padre_id');

        if ($comentarioPadreId) {
            $comentarioPadre = DB::table('comentarios_extravio')
                ->where('id_comentario', $comentarioPadreId)
                ->where('publicacion_id', $publicacion->id_publicacion)
                ->first();

            if (!$comentarioPadre) {
                return back()->withErrors(['comentario' => 'El comentario al que intentas responder no existe.']);
            }
        }

        DB::table('comentarios_extravio')->insert([
            'publicacion_id' => $publicacion->id_publicacion,
            'usuario_id' => Auth::id(),
            'comentario_padre_id' => $comentarioPadreId,
            'comentario' => trim($request->comentario),
            'estado' => 'VISIBLE',
            'creado_en' => now(),
            'actualizado_en' => now(),
        ]);

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
            ->with('success', 'Comentario publicado correctamente.');
    }

    public function updateComment(Request $request, $id, $comentarioId)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        $comentario = DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->first();

        if (!$comentario) {
            abort(404);
        }

        if ((int) $comentario->usuario_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($comentario->estado !== 'VISIBLE') {
            return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
                ->withErrors(['comentario' => 'Este comentario ya no puede editarse.']);
        }

        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->update([
                'comentario' => trim($request->comentario),
                'actualizado_en' => now(),
            ]);

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
            ->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroyComment($id, $comentarioId)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        $comentario = DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->first();

        if (!$comentario) {
            abort(404);
        }

        if ((int) $comentario->usuario_id !== (int) Auth::id()) {
            abort(403);
        }

        DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->update([
                'estado' => 'ELIMINADO',
                'comentario' => 'Comentario eliminado',
                'actualizado_en' => now(),
            ]);

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
            ->with('success', 'Comentario eliminado correctamente.');
    }

    public function storeReport(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        if ((int) Auth::id() === (int) $publicacion->autor_usuario_id) {
            return back()
                ->withErrors(['reporte' => 'No puedes reportar tu propia publicación.'], 'reportarPublicacion')
                ->withInput();
        }

        $validator = validator($request->all(), [
            'motivo_id' => 'required|integer|exists:motivos_reporte,id_motivo',
            'descripcion_adicional' => 'nullable|string|max:500',
        ], [
            'motivo_id.required' => 'Selecciona un motivo.',
            'motivo_id.exists' => 'El motivo seleccionado no es válido.',
            'descripcion_adicional.max' => 'La descripción adicional no debe exceder 500 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'reportarPublicacion')
                ->withInput();
        }

        $motivoExiste = DB::table('motivos_reporte')
            ->where('id_motivo', $request->motivo_id)
            ->exists();

        if (!$motivoExiste) {
            return back()
                ->withErrors(['reporte' => 'El motivo seleccionado no es válido.'], 'reportarPublicacion')
                ->withInput();
        }

        $yaReportado = DB::table('reportes')
            ->where('reportante_usuario_id', Auth::id())
            ->where('objetivo_tipo', 'PUB_EXTRAVIO')
            ->where('objetivo_id', $publicacion->id_publicacion)
            ->whereIn('estado', ['ENVIADO', 'EN_REVISION'])
            ->exists();

        if ($yaReportado) {
            return back()
                ->withErrors(['reporte' => 'Ya enviaste un reporte para esta publicación y aún está en revisión.'], 'reportarPublicacion')
                ->withInput();
        }

        DB::table('reportes')->insert([
            'reportante_usuario_id' => Auth::id(),
            'objetivo_tipo' => 'PUB_EXTRAVIO',
            'objetivo_id' => $publicacion->id_publicacion,
            'motivo_id' => $request->motivo_id,
            'descripcion_adicional' => $request->descripcion_adicional ? trim($request->descripcion_adicional) : null,
            'estado' => 'ENVIADO',
            'revisado_por' => null,
            'revisado_en' => null,
            'nota_resolucion' => null,
            'creado_en' => now(),
        ]);

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#acciones-publicacion')
            ->with('success_reporte', 'Reporte enviado correctamente.');
    }
}