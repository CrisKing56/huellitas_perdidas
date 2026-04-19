<?php

namespace App\Http\Controllers;

use App\Models\AvistamientoExtravio;
use App\Models\PublicacionExtravio;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExtravioController extends Controller
{
    public function create()
    {
        $especies = collect();
        $razas = collect();

        if (Schema::hasTable('especies')) {
            $especies = DB::table('especies')
                ->where('activo', 1)
                ->orderBy('nombre', 'asc')
                ->get();
        }

        if (Schema::hasTable('razas')) {
            $razas = DB::table('razas')
                ->select('id_raza', 'especie_id', 'nombre')
                ->orderBy('nombre', 'asc')
                ->get();
        }

        return view('reportar-mascota', compact('especies', 'razas'));
    }

    public function store(Request $request)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'especie_id' => 'required|integer|exists:especies,id_especie',
            'raza_id' => 'nullable|integer|exists:razas,id_raza',
            'otra_raza' => 'nullable|string|max:80',
            'color' => 'required|string|max:80',
            'tamano' => 'required|in:PEQUEÑO,MEDIANO,GRANDE',
            'sexo' => 'required|in:MACHO,HEMBRA,DESCONOCIDO',
            'fecha_extravio' => 'required|date',
            'colonia_barrio' => 'required|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion' => 'required|string',
            'fotos' => 'required|array|min:1|max:8',
            'fotos.*' => 'image|max:5120',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ], [
            'nombre.required' => 'Debes escribir el nombre de la mascota.',
            'especie_id.required' => 'Debes seleccionar una especie.',
            'especie_id.exists' => 'La especie seleccionada no es válida.',
            'raza_id.exists' => 'La raza seleccionada no es válida.',
            'otra_raza.max' => 'La otra raza no debe exceder 80 caracteres.',
            'color.required' => 'Debes indicar el color de la mascota.',
            'tamano.required' => 'Debes seleccionar el tamaño.',
            'tamano.in' => 'El tamaño seleccionado no es válido.',
            'sexo.required' => 'Debes seleccionar el sexo.',
            'sexo.in' => 'El sexo seleccionado no es válido.',
            'fecha_extravio.required' => 'Debes indicar la fecha de extravío.',
            'fecha_extravio.date' => 'La fecha de extravío no es válida.',
            'colonia_barrio.required' => 'Debes indicar la colonia o barrio.',
            'descripcion.required' => 'Debes escribir una descripción.',
            'fotos.required' => 'Debes subir al menos una fotografía.',
            'fotos.array' => 'Las fotografías no tienen un formato válido.',
            'fotos.min' => 'Debes subir al menos una fotografía.',
            'fotos.max' => 'Solo puedes subir hasta 8 fotografías.',
            'fotos.*.image' => 'Cada archivo debe ser una imagen.',
            'fotos.*.max' => 'Cada fotografía no debe pesar más de 5 MB.',
            'latitud.required' => 'Debes marcar una ubicación en el mapa.',
            'longitud.required' => 'Debes marcar una ubicación en el mapa.',
        ]);

        if (empty($request->raza_id) && empty(trim((string) $request->otra_raza))) {
            return back()
                ->withInput()
                ->withErrors(['raza_id' => 'Debes seleccionar una raza o escribir una si no aparece en la lista.']);
        }

        if (!empty($request->raza_id) && Schema::hasTable('razas') && Schema::hasColumn('razas', 'especie_id')) {
            $razaValida = DB::table('razas')
                ->where('id_raza', $request->raza_id)
                ->where('especie_id', $request->especie_id)
                ->exists();

            if (!$razaValida) {
                return back()
                    ->withInput()
                    ->withErrors(['raza_id' => 'La raza seleccionada no corresponde a la especie elegida.']);
            }
        }

        try {
            $publicacionCreada = null;

            DB::transaction(function () use ($request, &$publicacionCreada) {
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

                $fotos = $request->file('fotos', []);
                foreach ($fotos as $index => $foto) {
                    $rutaFoto = $foto->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacion->id_publicacion,
                        'url' => $rutaFoto,
                        'orden' => $index + 1,
                    ]);
                }

                $publicacionCreada = $publicacion;
            });

            if ($publicacionCreada) {
                $this->enviarCorreoPublicacionCreada($publicacionCreada);
            }

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

        $baseQuery = PublicacionExtravio::where('autor_usuario_id', $userId);

        $mascotas = (clone $baseQuery)
            ->with(['fotos', 'fotoPrincipal'])
            ->orderBy('id_publicacion', 'desc')
            ->paginate(10);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'activas' => (clone $baseQuery)->where('estado', 'ACTIVA')->count(),
            'resueltas' => (clone $baseQuery)->where('estado', 'RESUELTA')->count(),
        ];

        return view('extravios.mis-reportes', compact('mascotas', 'stats'));
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

        if ($filtros['raza'] !== '' && Schema::hasColumn('publicaciones_extravio', 'raza_id')) {
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
                $query->orderBy('id_publicacion', 'asc');
                break;
            case 'nombre_az':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_za':
                $query->orderBy('nombre', 'desc');
                break;
            case 'recientes':
            default:
                $query->orderBy('id_publicacion', 'desc');
                break;
        }

        $mascotas = $query->paginate(12)->appends($request->query());

        $especies = collect();
        if (Schema::hasTable('especies')) {
            $especies = DB::table('especies')
                ->where('activo', 1)
                ->select('id_especie', 'nombre')
                ->orderBy('nombre', 'asc')
                ->get();
        }

        $razas = collect();
        if (Schema::hasTable('razas')) {
            $queryRazas = DB::table('razas')
                ->select('id_raza', 'nombre', 'especie_id');

            if ($filtros['especie'] !== '' && Schema::hasColumn('razas', 'especie_id')) {
                $queryRazas->where('especie_id', $filtros['especie']);
            }

            $razas = $queryRazas
                ->orderBy('nombre', 'asc')
                ->get();
        }

        $mapaEspecies = $especies->pluck('nombre', 'id_especie');
        $mapaRazas = $razas->pluck('nombre', 'id_raza');

        $mascotas->getCollection()->transform(function ($mascota) use ($mapaEspecies, $mapaRazas) {
            $mascota->especie_nombre = $mapaEspecies[$mascota->especie_id] ?? 'Mascota';
            $mascota->raza_nombre = isset($mascota->raza_id)
                ? ($mapaRazas[$mascota->raza_id] ?? null)
                : null;

            return $mascota;
        });

        $conteos = [
            'todas' => PublicacionExtravio::count(),
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

    public function edit($id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::with(['fotoPrincipal', 'ubicacion', 'fotos'])->findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No tienes permiso para editar esta publicación.');
        }

        $especies = collect();
        $razas = collect();

        if (Schema::hasTable('especies')) {
            $especies = DB::table('especies')
                ->where('activo', 1)
                ->orderBy('nombre', 'asc')
                ->get();
        }

        if (Schema::hasTable('razas')) {
            $razas = DB::table('razas')
                ->select('id_raza', 'especie_id', 'nombre')
                ->orderBy('nombre', 'asc')
                ->get();
        }

        return view('extravios.editar', compact('publicacion', 'especies', 'razas'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::with(['ubicacion', 'fotos'])->findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No puedes editar esto.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'especie_id' => 'required|integer|exists:especies,id_especie',
            'raza_id' => 'nullable|integer|exists:razas,id_raza',
            'otra_raza' => 'nullable|string|max:80',
            'color' => 'required|string|max:80',
            'tamano' => 'required|in:PEQUEÑO,MEDIANO,GRANDE',
            'sexo' => 'required|in:MACHO,HEMBRA,DESCONOCIDO',
            'fecha_extravio' => 'required|date',
            'colonia_barrio' => 'required|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion' => 'required|string',
            'fotos' => 'nullable|array|max:8',
            'fotos.*' => 'image|max:5120',
            'delete_fotos' => 'nullable|array',
            'delete_fotos.*' => 'integer|exists:extravio_fotos,id_foto',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ], [
            'nombre.required' => 'Debes escribir el nombre de la mascota.',
            'especie_id.required' => 'Debes seleccionar una especie.',
            'raza_id.exists' => 'La raza seleccionada no es válida.',
            'color.required' => 'Debes indicar el color.',
            'tamano.required' => 'Debes seleccionar el tamaño.',
            'sexo.required' => 'Debes seleccionar el sexo.',
            'fecha_extravio.required' => 'Debes indicar la fecha de extravío.',
            'colonia_barrio.required' => 'Debes indicar la colonia o barrio.',
            'descripcion.required' => 'Debes escribir una descripción.',
            'fotos.array' => 'Las fotografías no tienen un formato válido.',
            'fotos.max' => 'Solo puedes subir hasta 8 fotografías.',
            'fotos.*.image' => 'Cada archivo debe ser una imagen.',
            'fotos.*.max' => 'Cada fotografía no debe pesar más de 5 MB.',
            'latitud.required' => 'Debes marcar una ubicación en el mapa.',
            'longitud.required' => 'Debes marcar una ubicación en el mapa.',
        ]);

        if (empty($request->raza_id) && empty(trim((string) $request->otra_raza))) {
            return back()
                ->withInput()
                ->withErrors(['raza_id' => 'Debes seleccionar una raza o escribir una si no aparece en la lista.']);
        }

        if (!empty($request->raza_id) && Schema::hasTable('razas') && Schema::hasColumn('razas', 'especie_id')) {
            $razaValida = DB::table('razas')
                ->where('id_raza', $request->raza_id)
                ->where('especie_id', $request->especie_id)
                ->exists();

            if (!$razaValida) {
                return back()
                    ->withInput()
                    ->withErrors(['raza_id' => 'La raza seleccionada no corresponde a la especie elegida.']);
            }
        }

        $deleteFotosIds = collect($request->input('delete_fotos', []))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $fotosActuales = DB::table('extravio_fotos')
            ->where('publicacion_id', $publicacion->id_publicacion)
            ->orderBy('orden')
            ->get();

        $fotosActualesIds = $fotosActuales->pluck('id_foto')->map(fn($id) => (int) $id);
        $deleteFotosIds = $deleteFotosIds->filter(fn($id) => $fotosActualesIds->contains($id))->values();

        $cantidadRestante = $fotosActuales->count() - $deleteFotosIds->count();
        $nuevasFotos = $request->file('fotos', []);
        $cantidadNuevas = is_array($nuevasFotos) ? count($nuevasFotos) : 0;
        $cantidadFinal = $cantidadRestante + $cantidadNuevas;

        if ($cantidadFinal < 1) {
            return back()
                ->withInput()
                ->withErrors(['fotos' => 'La publicación debe conservar al menos una fotografía.']);
        }

        if ($cantidadFinal > 8) {
            return back()
                ->withInput()
                ->withErrors(['fotos' => 'Solo puedes tener hasta 8 fotografías en total.']);
        }

        DB::transaction(function () use ($request, $publicacion, $deleteFotosIds, $nuevasFotos) {
            if ($publicacion->ubicacion_id) {
                DB::table('ubicaciones')
                    ->where('id_ubicacion', $publicacion->ubicacion_id)
                    ->update([
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                    ]);
            } else {
                $nuevaUbicacion = Ubicacion::create([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'precision_metros' => null,
                ]);

                $publicacion->ubicacion_id = $nuevaUbicacion->id_ubicacion;
            }

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
                'ubicacion_id' => $publicacion->ubicacion_id,
            ];

            if (Schema::hasColumn('publicaciones_extravio', 'raza_id')) {
                $data['raza_id'] = $request->raza_id ?: null;
            }

            if (Schema::hasColumn('publicaciones_extravio', 'otra_raza')) {
                $data['otra_raza'] = $request->otra_raza ?: null;
            }

            $publicacion->update($data);

            if ($deleteFotosIds->isNotEmpty()) {
                $fotosAEliminar = DB::table('extravio_fotos')
                    ->where('publicacion_id', $publicacion->id_publicacion)
                    ->whereIn('id_foto', $deleteFotosIds)
                    ->get();

                foreach ($fotosAEliminar as $foto) {
                    if ($foto->url && Storage::disk('public')->exists($foto->url)) {
                        Storage::disk('public')->delete($foto->url);
                    }
                }

                DB::table('extravio_fotos')
                    ->where('publicacion_id', $publicacion->id_publicacion)
                    ->whereIn('id_foto', $deleteFotosIds)
                    ->delete();
            }

            $fotosExistentes = DB::table('extravio_fotos')
                ->where('publicacion_id', $publicacion->id_publicacion)
                ->orderBy('orden')
                ->get();

            $orden = 1;

            foreach ($fotosExistentes as $foto) {
                DB::table('extravio_fotos')
                    ->where('id_foto', $foto->id_foto)
                    ->update(['orden' => $orden]);
                $orden++;
            }

            if (is_array($nuevasFotos) && count($nuevasFotos) > 0) {
                foreach ($nuevasFotos as $foto) {
                    $rutaFoto = $foto->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacion->id_publicacion,
                        'url' => $rutaFoto,
                        'orden' => $orden,
                    ]);

                    $orden++;
                }
            }
        });

        return redirect()->route('extravios.index')->with('success', '¡Mascota actualizada!');
    }

    public function marcarResuelta($id)
    {
        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $publicacion = PublicacionExtravio::findOrFail($id);
        $userId = Auth::user()->id_usuario;

        if ((int) $userId !== (int) $publicacion->autor_usuario_id) {
            return redirect()->route('extravios.index')->with('error', 'No tienes permiso para cambiar el estado de esta publicación.');
        }

        if ($publicacion->estado === 'RESUELTA') {
            return redirect()->route('extravios.index')->with('success', 'La publicación ya estaba marcada como resuelta.');
        }

        $data = [
            'estado' => 'RESUELTA',
        ];

        if (Schema::hasColumn('publicaciones_extravio', 'resuelta_en')) {
            $data['resuelta_en'] = now();
        }

        $publicacion->update($data);

        return redirect()->route('extravios.index')->with('success', 'La publicación se marcó como resuelta correctamente.');
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

        $fotos = DB::table('extravio_fotos')->where('publicacion_id', $id)->get();

        foreach ($fotos as $foto) {
            if ($foto->url && Storage::disk('public')->exists($foto->url)) {
                Storage::disk('public')->delete($foto->url);
            }
        }

        DB::table('extravio_fotos')->where('publicacion_id', $id)->delete();
        $publicacion->delete();

        return back()->with('success', 'Reporte eliminado correctamente.');
    }

    public function show($id)
    {
        $publicacion = PublicacionExtravio::with(['autor', 'fotoPrincipal', 'ubicacion', 'fotos'])->findOrFail($id);

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

        $razaTexto = null;
        if (!empty($publicacion->raza_id) && Schema::hasTable('razas')) {
            $razaTexto = DB::table('razas')
                ->where('id_raza', $publicacion->raza_id)
                ->value('nombre');
        }

        $especieTexto = match ((int) $publicacion->especie_id) {
            1 => 'Perro',
            2 => 'Gato',
            default => 'Mascota',
        };

        $avistamientos = collect();

        if (
            Schema::hasTable('avistamientos_extravio') &&
            Auth::check() &&
            (int) Auth::user()->id_usuario === (int) $publicacion->autor_usuario_id
        ) {
            $avistamientos = DB::table('avistamientos_extravio as a')
                ->leftJoin('usuarios as u', 'u.id_usuario', '=', 'a.usuario_reportante_id')
                ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'a.ubicacion_id')
                ->where('a.publicacion_id', $id)
                ->select(
                    'a.*',
                    'u.nombre as usuario_nombre',
                    'ub.latitud',
                    'ub.longitud'
                )
                ->orderByDesc('a.creado_en')
                ->get();
        }

        $mascotasRelacionadas = PublicacionExtravio::with('fotoPrincipal')
            ->where('id_publicacion', '!=', $publicacion->id_publicacion)
            ->whereNotIn('estado', ['RESUELTA', 'ELIMINADA'])
            ->where('especie_id', $publicacion->especie_id)
            ->orderByDesc('id_publicacion')
            ->take(4)
            ->get();

        if ($mascotasRelacionadas->count() < 4) {
            $idsExcluidos = $mascotasRelacionadas
                ->pluck('id_publicacion')
                ->push($publicacion->id_publicacion)
                ->values();

            $faltantes = 4 - $mascotasRelacionadas->count();

            $extra = PublicacionExtravio::with('fotoPrincipal')
                ->whereNotIn('id_publicacion', $idsExcluidos)
                ->whereNotIn('estado', ['RESUELTA', 'ELIMINADA'])
                ->orderByDesc('id_publicacion')
                ->take($faltantes)
                ->get();

            $mascotasRelacionadas = $mascotasRelacionadas->concat($extra);
        }

        return view('mascota-detalle', compact(
            'publicacion',
            'comentarios',
            'respuestasPorPadre',
            'motivosReporte',
            'avistamientos',
            'mascotasRelacionadas',
            'especieTexto',
            'razaTexto'
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

        $this->enviarCorreoNuevoComentario($publicacion, $userId, trim($request->comentario));

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

        $descripcionAdicional = $request->descripcion_adicional ? trim($request->descripcion_adicional) : null;

        DB::table('reportes')->insert([
            'reportante_usuario_id' => $userId,
            'objetivo_tipo' => 'PUB_EXTRAVIO',
            'objetivo_id' => $publicacion->id_publicacion,
            'motivo_id' => $request->motivo_id,
            'descripcion_adicional' => $descripcionAdicional,
            'estado' => 'ENVIADO',
            'revisado_por' => null,
            'revisado_en' => null,
            'nota_resolucion' => null,
            'creado_en' => now(),
        ]);

        $this->enviarCorreoPublicacionReportada(
            $publicacion,
            $userId,
            (int) $request->motivo_id,
            $descripcionAdicional
        );

        return redirect(route('extravios.show', $publicacion->id_publicacion) . '#acciones-publicacion')
            ->with('success_reporte', 'Reporte enviado correctamente.');
    }

    public function storeSighting(Request $request, $id)
    {
        if (!Schema::hasTable('avistamientos_extravio')) {
            return back()->withErrors([
                'error' => 'La tabla de avistamientos aún no existe. Ejecuta primero la migración.'
            ]);
        }

        $publicacion = PublicacionExtravio::with('autor')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        if ((int) Auth::user()->id_usuario === (int) $publicacion->autor_usuario_id) {
            return back()->withErrors([
                'error' => 'No puedes reportar un avistamiento en tu propia publicación.'
            ]);
        }

        $request->validate([
            'nombre_contacto' => 'nullable|string|max:120',
            'telefono_contacto' => 'nullable|string|max:20',
            'fecha_avistamiento' => 'nullable|date',
            'colonia_barrio' => 'nullable|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion_avistamiento' => 'required|string|max:1500',
            'foto_avistamiento' => 'nullable|image|max:5120',
            'latitud_avistamiento' => 'nullable|numeric',
            'longitud_avistamiento' => 'nullable|numeric',
        ], [
            'descripcion_avistamiento.required' => 'Debes escribir una descripción del avistamiento.',
            'descripcion_avistamiento.max' => 'La descripción del avistamiento no debe exceder 1500 caracteres.',
            'foto_avistamiento.image' => 'La foto del avistamiento debe ser una imagen.',
            'foto_avistamiento.max' => 'La foto del avistamiento no debe pesar más de 5 MB.',
        ]);

        if (
            ($request->filled('latitud_avistamiento') && !$request->filled('longitud_avistamiento')) ||
            (!$request->filled('latitud_avistamiento') && $request->filled('longitud_avistamiento'))
        ) {
            return back()
                ->withInput()
                ->withErrors(['descripcion_avistamiento' => 'Si marcas ubicación del avistamiento, debes enviar latitud y longitud completas.']);
        }

        try {
            $avistamiento = null;

            DB::transaction(function () use ($request, $publicacion, &$avistamiento) {
                $ubicacionId = null;

                if ($request->filled('latitud_avistamiento') && $request->filled('longitud_avistamiento')) {
                    $ubicacion = Ubicacion::create([
                        'latitud' => $request->latitud_avistamiento,
                        'longitud' => $request->longitud_avistamiento,
                        'precision_metros' => null,
                    ]);

                    $ubicacionId = $ubicacion->id_ubicacion;
                }

                $fotoUrl = null;
                if ($request->hasFile('foto_avistamiento')) {
                    $fotoUrl = $request->file('foto_avistamiento')->store('avistamientos', 'public');
                }

                $avistamiento = AvistamientoExtravio::create([
                    'publicacion_id' => $publicacion->id_publicacion,
                    'usuario_reportante_id' => Auth::user()->id_usuario,
                    'ubicacion_id' => $ubicacionId,
                    'nombre_contacto' => $request->nombre_contacto ?: (Auth::user()->nombre ?? null),
                    'telefono_contacto' => $request->telefono_contacto ?: (Auth::user()->telefono ?? null),
                    'fecha_avistamiento' => $request->fecha_avistamiento ?: null,
                    'colonia_barrio' => $request->colonia_barrio ?: null,
                    'calle_referencias' => $request->calle_referencias ?: null,
                    'descripcion' => trim($request->descripcion_avistamiento),
                    'foto_url' => $fotoUrl,
                    'estado' => 'ENVIADO',
                    'creado_en' => now(),
                ]);
            });

            if ($avistamiento) {
                $this->enviarCorreoAvistamiento($publicacion, $avistamiento);
            }

            return redirect(route('extravios.show', $publicacion->id_publicacion) . '#bloque-avistamiento')
                ->with('success_avistamiento', 'Avistamiento enviado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => 'No se pudo guardar el avistamiento: ' . $e->getMessage()
            ]);
        }
    }

    public function markSightingSeen($id)
    {
        if (!Schema::hasTable('avistamientos_extravio')) {
            return back()->withErrors([
                'error' => 'La tabla de avistamientos aún no existe.'
            ]);
        }

        $avistamiento = AvistamientoExtravio::with('publicacion')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        if ((int) Auth::user()->id_usuario !== (int) $avistamiento->publicacion->autor_usuario_id) {
            abort(403);
        }

        if ($avistamiento->estado !== 'VISTO') {
            $avistamiento->update([
                'estado' => 'VISTO',
                'visto_en' => now(),
            ]);
        }

        return back()->with('success_avistamiento', 'El avistamiento fue marcado como visto.');
    }

    public function discardSighting($id)
    {
        if (!Schema::hasTable('avistamientos_extravio')) {
            return back()->withErrors([
                'error' => 'La tabla de avistamientos aún no existe.'
            ]);
        }

        $avistamiento = AvistamientoExtravio::with('publicacion')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        if ((int) Auth::user()->id_usuario !== (int) $avistamiento->publicacion->autor_usuario_id) {
            abort(403);
        }

        $avistamiento->update([
            'estado' => 'DESCARTADO',
        ]);

        return back()->with('success_avistamiento', 'El avistamiento fue descartado.');
    }

    private function enviarCorreoPublicacionCreada(PublicacionExtravio $publicacion): void
    {
        try {
            $usuario = DB::table('usuarios')
                ->where('id_usuario', $publicacion->autor_usuario_id)
                ->select('nombre', 'correo')
                ->first();

            if (!$usuario || empty($usuario->correo)) {
                return;
            }

            $especieNombre = null;
            if (!empty($publicacion->especie_id) && Schema::hasTable('especies')) {
                $especieNombre = DB::table('especies')
                    ->where('id_especie', $publicacion->especie_id)
                    ->value('nombre');
            }

            $urlPublicacion = route('extravios.show', $publicacion->id_publicacion);

            $html = view('emails.extravio-publicado', [
                'usuarioNombre' => $usuario->nombre,
                'publicacion' => $publicacion,
                'especieNombre' => $especieNombre,
                'urlPublicacion' => $urlPublicacion,
            ])->render();

            Mail::html($html, function ($message) use ($usuario) {
                $message->to($usuario->correo, $usuario->nombre)
                    ->subject('Tu reporte de mascota extraviada fue publicado');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoNuevoComentario(PublicacionExtravio $publicacion, int $comentarioUsuarioId, string $comentarioTexto): void
    {
        try {
            if ((int) $publicacion->autor_usuario_id === (int) $comentarioUsuarioId) {
                return;
            }

            $dueno = DB::table('usuarios')
                ->where('id_usuario', $publicacion->autor_usuario_id)
                ->select('id_usuario', 'nombre', 'correo')
                ->first();

            if (!$dueno || empty($dueno->correo)) {
                return;
            }

            $autorComentario = DB::table('usuarios')
                ->where('id_usuario', $comentarioUsuarioId)
                ->select('nombre')
                ->first();

            $urlPublicacion = route('extravios.show', $publicacion->id_publicacion) . '#comentarios';

            $html = view('emails.extravio-comentario', [
                'duenoNombre' => $dueno->nombre,
                'autorComentarioNombre' => $autorComentario->nombre ?? 'Un usuario',
                'publicacion' => $publicacion,
                'comentarioTexto' => $comentarioTexto,
                'urlPublicacion' => $urlPublicacion,
            ])->render();

            Mail::html($html, function ($message) use ($dueno) {
                $message->to($dueno->correo, $dueno->nombre)
                    ->subject('Nuevo comentario en tu publicación de mascota extraviada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoPublicacionReportada(
        PublicacionExtravio $publicacion,
        int $reportanteUsuarioId,
        int $motivoId,
        ?string $descripcionAdicional
    ): void {
        try {
            if ((int) $publicacion->autor_usuario_id === (int) $reportanteUsuarioId) {
                return;
            }

            $dueno = DB::table('usuarios')
                ->where('id_usuario', $publicacion->autor_usuario_id)
                ->select('id_usuario', 'nombre', 'correo')
                ->first();

            if (!$dueno || empty($dueno->correo)) {
                return;
            }

            $motivoNombre = DB::table('motivos_reporte')
                ->where('id_motivo', $motivoId)
                ->value('nombre');

            $urlPublicacion = route('extravios.show', $publicacion->id_publicacion);

            $html = view('emails.extravio-reportado', [
                'duenoNombre' => $dueno->nombre,
                'publicacion' => $publicacion,
                'motivoNombre' => $motivoNombre,
                'descripcionAdicional' => $descripcionAdicional,
                'urlPublicacion' => $urlPublicacion,
            ])->render();

            Mail::html($html, function ($message) use ($dueno) {
                $message->to($dueno->correo, $dueno->nombre)
                    ->subject('Tu publicación recibió un reporte');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoAvistamiento(PublicacionExtravio $publicacion, AvistamientoExtravio $avistamiento): void
    {
        try {
            $dueno = DB::table('usuarios')
                ->where('id_usuario', $publicacion->autor_usuario_id)
                ->select('nombre', 'correo')
                ->first();

            if (!$dueno || empty($dueno->correo)) {
                return;
            }

            $urlPublicacion = route('extravios.show', $publicacion->id_publicacion) . '#avistamientos';

            $html = view('emails.avistamiento-recibido', [
                'duenoNombre' => $dueno->nombre,
                'publicacion' => $publicacion,
                'avistamiento' => $avistamiento,
                'urlPublicacion' => $urlPublicacion,
            ])->render();

            Mail::html($html, function ($message) use ($dueno, $publicacion) {
                $message->to($dueno->correo, $dueno->nombre)
                    ->subject('Nuevo avistamiento reportado de ' . $publicacion->nombre);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}