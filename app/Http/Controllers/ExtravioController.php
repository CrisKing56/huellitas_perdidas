<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Ubicacion;
use App\Models\PublicacionExtravio;

class ExtravioController extends Controller
{
    public function create()
    {
        return view('reportar-mascota');
    }

    public function store(Request $request)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required',
            'foto' => 'required|image|max:5120',
            'colonia_barrio' => 'required',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userId = Auth::user()->id_usuario;

                $nuevaUbicacion = Ubicacion::create([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'precision_metros' => null,
                ]);

                $data = [
                    'autor_usuario_id' => $userId,
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
                    'estado' => 'ACTIVA',
                ];

                if (Schema::hasColumn('publicaciones_extravio', 'raza_id')) {
                    $data['raza_id'] = $request->raza_id ?: null;
                }

                if (Schema::hasColumn('publicaciones_extravio', 'otra_raza')) {
                    $data['otra_raza'] = $request->otra_raza ?: null;
                }

                $publicacion = PublicacionExtravio::create($data);

                if ($request->hasFile('foto')) {
                    $rutaFoto = $request->file('foto')->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacion->id_publicacion,
                        'url' => $rutaFoto,
                        'orden' => 1,
                    ]);
                }
            });

            return redirect()->route('mascotas.index2')->with('success', '¡Reporte guardado exitosamente!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Hubo un problema al guardar el reporte: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id_usuario;

        $mascotas = PublicacionExtravio::where('autor_usuario_id', $userId)
            ->with('fotos')
            ->orderBy('id_publicacion', 'desc')
            ->paginate(10);

        return view('extravios.mis-reportes', compact('mascotas'));
    }

    public function index2(Request $request)
    {
        $filtros = [
            'q' => trim((string) $request->get('q', '')),
            'estado' => (string) $request->get('estado', ''),
            'especie' => (string) $request->get('especie', ''),
            'raza' => (string) $request->get('raza', ''),
            'sexo' => (string) $request->get('sexo', ''),
            'tamano' => (string) $request->get('tamano', ''),
            'colonia' => trim((string) $request->get('colonia', '')),
            'orden' => (string) $request->get('orden', 'recientes'),
        ];

        $query = PublicacionExtravio::with(['fotoPrincipal', 'ubicacion']);

        if ($filtros['q'] !== '') {
            $query->where(function ($sub) use ($filtros) {
                $sub->where('nombre', 'like', '%' . $filtros['q'] . '%')
                    ->orWhere('descripcion', 'like', '%' . $filtros['q'] . '%')
                    ->orWhere('colonia_barrio', 'like', '%' . $filtros['q'] . '%');
            });
        }

        if ($filtros['estado'] !== '') {
            $query->where('estado', $filtros['estado']);
        }

        if ($filtros['especie'] !== '') {
            $query->where('especie_id', $filtros['especie']);
        }

        if (
            $filtros['raza'] !== '' &&
            Schema::hasColumn('publicaciones_extravio', 'raza_id')
        ) {
            $query->where('raza_id', $filtros['raza']);
        }

        if ($filtros['sexo'] !== '') {
            $query->where('sexo', $filtros['sexo']);
        }

        if ($filtros['tamano'] !== '') {
            $query->where('tamano', $filtros['tamano']);
        }

        if ($filtros['colonia'] !== '') {
            $query->where('colonia_barrio', 'like', '%' . $filtros['colonia'] . '%');
        }

        switch ($filtros['orden']) {
            case 'antiguos':
                if (Schema::hasColumn('publicaciones_extravio', 'creado_en')) {
                    $query->orderBy('creado_en', 'asc');
                } else {
                    $query->orderBy('id_publicacion', 'asc');
                }
                break;

            case 'nombre_az':
                $query->orderBy('nombre', 'asc');
                break;

            case 'nombre_za':
                $query->orderBy('nombre', 'desc');
                break;

            case 'recientes':
            default:
                if (Schema::hasColumn('publicaciones_extravio', 'creado_en')) {
                    $query->orderBy('creado_en', 'desc');
                } else {
                    $query->orderBy('id_publicacion', 'desc');
                }
                break;
        }

        $mascotas = $query->paginate(12)->appends($request->query());

        // Catálogo de especies
        $especies = collect();
        if (Schema::hasTable('especies')) {
            $especies = DB::table('especies')
                ->select('id_especie', 'nombre')
                ->orderBy('nombre', 'asc')
                ->get();
        }

        // Catálogo de razas
        $razas = collect();
        if (Schema::hasTable('razas')) {
            $queryRazas = DB::table('razas')
                ->select('id_raza', 'nombre');

            if (
                $filtros['especie'] !== '' &&
                Schema::hasColumn('razas', 'especie_id')
            ) {
                $queryRazas->where('especie_id', $filtros['especie']);
            }

            $razas = $queryRazas
                ->orderBy('nombre', 'asc')
                ->get();
        }

        // Mapas para mostrar nombres en cards
        $mapaEspecies = $especies->pluck('nombre', 'id_especie');
        $mapaRazas = $razas->pluck('nombre', 'id_raza');

        $mascotas->getCollection()->transform(function ($mascota) use ($mapaEspecies, $mapaRazas) {
            $mascota->especie_nombre = $mapaEspecies[$mascota->especie_id] ?? 'Mascota';
            $mascota->raza_nombre = isset($mascota->raza_id)
                ? ($mapaRazas[$mascota->raza_id] ?? null)
                : null;

            return $mascota;
        });

        // Conteos que tu vista actual sí usa
        $conteos = [
            'todas' => PublicacionExtravio::count(),
            'activas' => PublicacionExtravio::where('estado', 'ACTIVA')->count(),
            'resueltas' => PublicacionExtravio::whereIn('estado', ['RESUELTA', 'ENCONTRADA'])->count(),
        ];

        return view('mascotas-perdidas', compact(
            'mascotas',
            'filtros',
            'conteos',
            'especies',
            'razas'
        ));
    }

    public function edit($id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No tienes permiso para editar esta publicación.');
        }

        return view('extravios.editar', compact('publicacion'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No puedes editar esto.');
        }

        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required',
            'colonia_barrio' => 'required',
            'foto' => 'nullable|image|max:5120',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'especie_id' => $request->especie_id,
            'color' => $request->color,
            'tamano' => $request->tamano,
            'sexo' => $request->sexo,
            'fecha_extravio' => $request->fecha_extravio,
            'colonia_barrio' => $request->colonia_barrio,
            'descripcion' => $request->descripcion,
            'calle_referencias' => $request->calle_referencias,
        ];

        if (Schema::hasColumn('publicaciones_extravio', 'raza_id')) {
            $data['raza_id'] = $request->raza_id ?: null;
        }

        if (Schema::hasColumn('publicaciones_extravio', 'otra_raza')) {
            $data['otra_raza'] = $request->otra_raza ?: null;
        }

        $publicacion->update($data);

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('mascotas', 'public');

            DB::table('extravio_fotos')->updateOrInsert(
                ['publicacion_id' => $publicacion->id_publicacion],
                ['url' => $rutaFoto, 'orden' => 1]
            );
        }

        return redirect()->route('extravios.index')->with('success', '¡Mascota actualizada!');
    }

    public function destroy($id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return back()->with('error', 'No tienes permiso para borrar esto.');
        }

        DB::table('extravio_fotos')->where('publicacion_id', $id)->delete();
        $publicacion->delete();

        return back()->with('success', 'Reporte eliminado correctamente.');
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

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $request->validate([
            'comentario' => 'required|string|max:1000',
            'comentario_padre_id' => 'nullable|integer'
        ]);

        $comentarioPadreId = $request->input('comentario_padre_id');
        $userId = Auth::user()->id_usuario;

        if ($comentarioPadreId) {
            $comentarioPadre = DB::table('comentarios_extravio')
                ->where('id_comentario', $comentarioPadreId)
                ->where('publicacion_id', $publicacion->id_publicacion)
                ->first();

            if (!$comentarioPadre) {
                return back()->withErrors([
                    'comentario' => 'El comentario al que intentas responder no existe.'
                ]);
            }
        }

        DB::table('comentarios_extravio')->insert([
            'publicacion_id' => $publicacion->id_publicacion,
            'usuario_id' => $userId,
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

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id_usuario;

        $comentario = DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->first();

        if (!$comentario) {
            abort(404);
        }

        if ((int) $comentario->usuario_id !== (int) $userId) {
            abort(403);
        }

        if ($comentario->estado !== 'VISIBLE') {
            return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
                ->withErrors([
                    'comentario' => 'Este comentario ya no puede editarse.'
                ]);
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

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id_usuario;

        $comentario = DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->first();

        if (!$comentario) {
            abort(404);
        }

        if ((int) $comentario->usuario_id !== (int) $userId) {
            abort(403);
        }

        DB::table('comentarios_extravio')
            ->where('id_comentario', $comentarioId)
            ->update([
                'estado' => 'ELIMINADO',
                'actualizado_en' => now(),
            ]);

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#comentarios')
            ->with('success', 'Comentario eliminado correctamente.');
    }

    public function storeReport(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $userId = Auth::user()->id_usuario;

        $validator = Validator::make($request->all(), [
            'motivo_id' => 'required|integer|exists:motivos_reporte,id_motivo',
            'descripcion_adicional' => 'nullable|string|max:500',
        ], [
            'motivo_id.required' => 'Debes seleccionar un motivo.',
            'motivo_id.integer' => 'El motivo seleccionado no es válido.',
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
            ->where('reportante_usuario_id', $userId)
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
            'reportante_usuario_id' => $userId,
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