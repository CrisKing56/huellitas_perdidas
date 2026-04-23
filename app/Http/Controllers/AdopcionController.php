<?php

namespace App\Http\Controllers;

use App\Models\AdopcionFoto;
use App\Models\Especie;
use App\Models\PublicacionAdopcion;
use App\Models\Raza;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdopcionController extends Controller
{
    private function usuarioIdActual()
    {
        return Auth::user()->id_usuario ?? null;
    }

    private function reglasValidacion(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'especie_id' => ['required', 'integer', Rule::exists('especies', 'id_especie')],
            'raza_id' => ['nullable', 'integer', Rule::exists('razas', 'id_raza')],
            'otra_raza' => ['nullable', 'string', 'max:80'],
            'edad_anios' => ['nullable', 'integer', 'min:0', 'max:30'],
            'sexo' => ['required', Rule::in(['MACHO', 'HEMBRA', 'DESCONOCIDO'])],
            'tamano' => ['required', Rule::in(['CHICO', 'MEDIANO', 'GRANDE', 'DESCONOCIDO'])],
            'color_predominante' => ['nullable', 'string', 'max:120'],
            'descripcion' => ['required', 'string'],
            'vacunas_aplicadas' => ['nullable', 'string'],
            'esterilizado' => ['nullable', 'boolean'],
            'condicion_salud' => ['nullable', 'string', 'max:120'],
            'descripcion_salud' => ['nullable', 'string'],
            'requisitos' => ['nullable', 'string'],
            'colonia_barrio' => ['nullable', 'string', 'max:120'],
            'calle_referencias' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],

            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'fotos' => ['nullable', 'array', 'max:8'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],

            'fotos_eliminar' => ['nullable', 'array'],
            'fotos_eliminar.*' => ['integer'],
        ];
    }

    private function mensajesValidacion(): array
    {
        return [
            'nombre.required' => 'El nombre de la mascota es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 100 caracteres.',

            'especie_id.required' => 'Debes seleccionar una especie.',
            'especie_id.integer' => 'La especie seleccionada no es válida.',
            'especie_id.exists' => 'La especie seleccionada no existe.',

            'raza_id.integer' => 'La raza seleccionada no es válida.',
            'raza_id.exists' => 'La raza seleccionada no existe.',

            'otra_raza.max' => 'La otra raza no debe superar los 80 caracteres.',
            'edad_anios.integer' => 'La edad debe ser un número entero.',
            'edad_anios.min' => 'La edad no puede ser negativa.',
            'edad_anios.max' => 'La edad no debe ser mayor a 30 años.',

            'sexo.required' => 'Debes seleccionar el sexo.',
            'sexo.in' => 'El sexo seleccionado no es válido.',

            'tamano.required' => 'Debes seleccionar el tamaño.',
            'tamano.in' => 'El tamaño seleccionado no es válido.',

            'color_predominante.max' => 'El color predominante no debe superar los 120 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'condicion_salud.max' => 'La condición de salud no debe superar los 120 caracteres.',
            'colonia_barrio.max' => 'La colonia o barrio no debe superar los 120 caracteres.',
            'calle_referencias.max' => 'Las referencias no deben superar los 255 caracteres.',

            'latitud.numeric' => 'La latitud debe ser numérica.',
            'latitud.between' => 'La latitud no es válida.',
            'longitud.numeric' => 'La longitud debe ser numérica.',
            'longitud.between' => 'La longitud no es válida.',

            'foto.image' => 'La foto principal debe ser una imagen válida.',
            'foto.mimes' => 'La foto principal debe estar en formato JPG, JPEG, PNG o WEBP.',
            'foto.max' => 'La foto principal debe pesar máximo 10 MB.',

            'fotos.array' => 'Las fotografías enviadas no son válidas.',
            'fotos.max' => 'Solo puedes subir hasta 8 fotografías.',
            'fotos.*.image' => 'Cada archivo debe ser una imagen válida.',
            'fotos.*.mimes' => 'Cada fotografía debe estar en formato JPG, JPEG, PNG o WEBP.',
            'fotos.*.max' => 'Cada fotografía debe pesar máximo 10 MB.',

            'fotos_eliminar.array' => 'Las fotos marcadas para eliminar no son válidas.',
            'fotos_eliminar.*.integer' => 'Una de las fotos marcadas para eliminar no es válida.',
        ];
    }

    private function atributosValidacion(): array
    {
        return [
            'nombre' => 'nombre',
            'especie_id' => 'especie',
            'raza_id' => 'raza',
            'otra_raza' => 'otra raza',
            'edad_anios' => 'edad',
            'sexo' => 'sexo',
            'tamano' => 'tamaño',
            'color_predominante' => 'color predominante',
            'descripcion' => 'descripción',
            'vacunas_aplicadas' => 'vacunas aplicadas',
            'esterilizado' => 'esterilizado',
            'condicion_salud' => 'condición de salud',
            'descripcion_salud' => 'descripción de salud',
            'requisitos' => 'requisitos',
            'colonia_barrio' => 'colonia o barrio',
            'calle_referencias' => 'calle y referencias',
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'foto' => 'foto principal',
            'fotos' => 'fotografías',
            'fotos.*' => 'fotografía',
            'fotos_eliminar' => 'fotos a eliminar',
            'fotos_eliminar.*' => 'foto a eliminar',
        ];
    }

    private function flattenFiles($files): array
    {
        $resultado = [];

        if ($files instanceof UploadedFile) {
            $resultado[] = $files;
            return $resultado;
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                $resultado = array_merge($resultado, $this->flattenFiles($file));
            }
        }

        return $resultado;
    }

    private function extraerArchivosFotos(Request $request): array
    {
        $archivos = [];

        foreach (['fotos', 'fotos[]', 'foto'] as $campo) {
            $valor = $request->file($campo);

            if ($valor) {
                $archivos = array_merge($archivos, $this->flattenFiles($valor));
            }
        }

        if (empty($archivos)) {
            foreach ($request->allFiles() as $valor) {
                $archivos = array_merge($archivos, $this->flattenFiles($valor));
            }
        }

        return array_values(array_filter($archivos, function ($archivo) {
            return $archivo instanceof UploadedFile;
        }));
    }

    public function index(Request $request)
    {
        $filtros = [
            'q' => trim((string) $request->get('q', '')),
            'especie' => (string) $request->get('especie', ''),
            'sexo' => (string) $request->get('sexo', ''),
            'tamano' => (string) $request->get('tamano', ''),
            'colonia' => trim((string) $request->get('colonia', '')),
            'orden' => (string) $request->get('orden', 'recientes'),
        ];

        $query = PublicacionAdopcion::with(['fotoPrincipal', 'especie', 'raza'])
            ->where('estado', 'DISPONIBLE')
            ->when($filtros['q'] !== '', function ($q) use ($filtros) {
                $q->where(function ($sub) use ($filtros) {
                    $sub->where('nombre', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('descripcion', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('otra_raza', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('colonia_barrio', 'like', '%' . $filtros['q'] . '%');
                });
            })
            ->when($filtros['especie'] !== '', function ($q) use ($filtros) {
                $q->where('especie_id', $filtros['especie']);
            })
            ->when($filtros['sexo'] !== '', function ($q) use ($filtros) {
                $q->whereRaw('UPPER(sexo) = ?', [mb_strtoupper($filtros['sexo'])]);
            })
            ->when($filtros['tamano'] !== '', function ($q) use ($filtros) {
                $q->whereRaw('UPPER(tamano) = ?', [mb_strtoupper($filtros['tamano'])]);
            })
            ->when($filtros['colonia'] !== '', function ($q) use ($filtros) {
                $q->where('colonia_barrio', 'like', '%' . $filtros['colonia'] . '%');
            });

        switch ($filtros['orden']) {
            case 'antiguos':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nombre_az':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_za':
                $query->orderBy('nombre', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $adopciones = $query->paginate(12)->withQueryString();

        $conteos = [
            'disponibles' => PublicacionAdopcion::where('estado', 'DISPONIBLE')->count(),
            'perros' => PublicacionAdopcion::where('estado', 'DISPONIBLE')->where('especie_id', 1)->count(),
            'gatos' => PublicacionAdopcion::where('estado', 'DISPONIBLE')->where('especie_id', 2)->count(),
        ];

        return view('mascotas-adopcion', compact('adopciones', 'filtros', 'conteos'));
    }

    public function misAdopciones()
    {
        $adopciones = PublicacionAdopcion::with(['fotoPrincipal', 'especie', 'raza'])
            ->where('autor_usuario_id', $this->usuarioIdActual())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('adopciones.mis-adopciones', compact('adopciones'));
    }

    public function create()
    {
        $especies = Especie::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $razas = Raza::orderBy('nombre')->get();

        return view('publicar-adopcion', compact('especies', 'razas'));
    }

    public function store(Request $request)
    {
        $request->validate(
            $this->reglasValidacion(),
            $this->mensajesValidacion(),
            $this->atributosValidacion()
        );

        if ($request->filled('raza_id')) {
            $raza = Raza::find($request->raza_id);

            if (!$raza || (int) $raza->especie_id !== (int) $request->especie_id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'La raza seleccionada no corresponde a la especie elegida.',
                        'errors' => [
                            'raza_id' => ['La raza seleccionada no corresponde a la especie elegida.']
                        ]
                    ], 422);
                }

                return back()
                    ->withErrors(['raza_id' => 'La raza seleccionada no corresponde a la especie elegida.'])
                    ->withInput();
            }
        }

        $archivos = $this->extraerArchivosFotos($request);

        if (count($archivos) === 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Debes subir al menos una fotografía.',
                    'errors' => [
                        'fotos' => ['Debes subir al menos una fotografía.']
                    ]
                ], 422);
            }

            return back()
                ->withErrors(['fotos' => 'Debes subir al menos una fotografía.'])
                ->withInput();
        }

        $adopcion = new PublicacionAdopcion();
        $adopcion->autor_usuario_id = $this->usuarioIdActual();
        $adopcion->nombre = $request->nombre;
        $adopcion->especie_id = $request->especie_id;
        $adopcion->raza_id = $request->filled('raza_id') ? $request->raza_id : null;
        $adopcion->otra_raza = $request->filled('raza_id') ? null : ($request->filled('otra_raza') ? trim($request->otra_raza) : null);
        $adopcion->edad_anios = $request->filled('edad_anios') ? $request->edad_anios : null;
        $adopcion->sexo = $request->sexo;
        $adopcion->tamano = $request->tamano;
        $adopcion->color_predominante = $request->filled('color_predominante') ? trim($request->color_predominante) : null;
        $adopcion->descripcion = $request->descripcion;
        $adopcion->vacunas_aplicadas = $request->filled('vacunas_aplicadas') ? trim($request->vacunas_aplicadas) : null;
        $adopcion->esterilizado = $request->filled('esterilizado') ? (int) $request->esterilizado : null;
        $adopcion->condicion_salud = $request->filled('condicion_salud') ? trim($request->condicion_salud) : null;
        $adopcion->descripcion_salud = $request->filled('descripcion_salud') ? trim($request->descripcion_salud) : null;
        $adopcion->requisitos = $request->filled('requisitos') ? trim($request->requisitos) : null;
        $adopcion->colonia_barrio = $request->filled('colonia_barrio') ? trim($request->colonia_barrio) : null;
        $adopcion->calle_referencias = $request->filled('calle_referencias') ? trim($request->calle_referencias) : null;
        $adopcion->latitud = $request->filled('latitud') ? $request->latitud : null;
        $adopcion->longitud = $request->filled('longitud') ? $request->longitud : null;
        $adopcion->estado = 'DISPONIBLE';
        $adopcion->save();

        foreach ($archivos as $index => $archivo) {
            $ruta = $archivo->store('adopciones', 'public');

            AdopcionFoto::create([
                'publicacion_id' => $adopcion->id_publicacion,
                'url' => $ruta,
                'orden' => $index + 1,
            ]);
        }

        if ($request->expectsJson()) {
            session()->flash('success', 'Mascota publicada para adopción correctamente.');

            return response()->json([
                'success' => true,
                'message' => 'Mascota publicada para adopción correctamente.',
                'redirect' => route('adopciones.mis-adopciones'),
            ]);
        }

        return redirect()
            ->route('adopciones.mis-adopciones')
            ->with('success', 'Mascota publicada para adopción correctamente.');
    }

    public function show($id)
    {
        $adopcion = PublicacionAdopcion::with(['autor', 'fotoPrincipal', 'fotos', 'especie', 'raza'])
            ->findOrFail($id);

        $puedeVerContacto = false;
        $solicitudAceptada = null;

        if (Auth::check()) {
            if ((int) $this->usuarioIdActual() === (int) $adopcion->autor_usuario_id) {
                $puedeVerContacto = true;
            } else {
                $solicitudAceptada = SolicitudAdopcion::where('publicacion_id', $adopcion->id_publicacion)
                    ->where('solicitante_usuario_id', $this->usuarioIdActual())
                    ->where('estado', 'ACEPTADA')
                    ->first();

                $puedeVerContacto = $solicitudAceptada !== null;
            }
        }

        $mascotasRelacionadas = PublicacionAdopcion::with(['fotoPrincipal', 'fotos', 'especie', 'raza'])
            ->where('estado', 'DISPONIBLE')
            ->where('id_publicacion', '!=', $adopcion->id_publicacion)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('adopciones.show', compact(
            'adopcion',
            'puedeVerContacto',
            'solicitudAceptada',
            'mascotasRelacionadas'
        ));
    }

    public function edit($id)
    {
        $adopcion = PublicacionAdopcion::with(['fotoPrincipal', 'fotos', 'especie', 'raza'])->findOrFail($id);

        if ((int) $this->usuarioIdActual() !== (int) $adopcion->autor_usuario_id) {
            return redirect()->route('adopciones.mis-adopciones');
        }

        $especies = Especie::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $razas = Raza::orderBy('nombre')->get();

        return view('adopciones.edit', compact('adopcion', 'especies', 'razas'));
    }

    public function update(Request $request, $id)
    {
        $adopcion = PublicacionAdopcion::with('fotos')->findOrFail($id);

        if ((int) $this->usuarioIdActual() !== (int) $adopcion->autor_usuario_id) {
            abort(403);
        }

        $request->validate(
            $this->reglasValidacion(),
            $this->mensajesValidacion(),
            $this->atributosValidacion()
        );

        if ($request->filled('raza_id')) {
            $raza = Raza::find($request->raza_id);

            if (!$raza || (int) $raza->especie_id !== (int) $request->especie_id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'La raza seleccionada no corresponde a la especie elegida.',
                        'errors' => [
                            'raza_id' => ['La raza seleccionada no corresponde a la especie elegida.']
                        ]
                    ], 422);
                }

                return back()
                    ->withErrors(['raza_id' => 'La raza seleccionada no corresponde a la especie elegida.'])
                    ->withInput();
            }
        }

        $idsEliminar = collect($request->input('fotos_eliminar', []))
            ->filter(fn($idFoto) => is_numeric($idFoto))
            ->map(fn($idFoto) => (int) $idFoto)
            ->unique()
            ->values();

        $fotosExistentes = $adopcion->fotos;
        $fotosParaEliminar = $fotosExistentes->filter(function ($foto) use ($idsEliminar) {
            return $idsEliminar->contains((int) $foto->id_foto);
        });

        $archivosNuevos = $this->extraerArchivosFotos($request);

        $totalFinalFotos = $fotosExistentes->count() - $fotosParaEliminar->count() + count($archivosNuevos);

        if ($totalFinalFotos < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'La publicación debe conservar al menos una fotografía.',
                    'errors' => [
                        'fotos' => ['La publicación debe conservar al menos una fotografía.']
                    ]
                ], 422);
            }

            return back()
                ->withErrors(['fotos' => 'La publicación debe conservar al menos una fotografía.'])
                ->withInput();
        }

        if ($totalFinalFotos > 8) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No puedes tener más de 8 fotografías en total.',
                    'errors' => [
                        'fotos' => ['No puedes tener más de 8 fotografías en total.']
                    ]
                ], 422);
            }

            return back()
                ->withErrors(['fotos' => 'No puedes tener más de 8 fotografías en total.'])
                ->withInput();
        }

        $adopcion->nombre = $request->nombre;
        $adopcion->especie_id = $request->especie_id;
        $adopcion->raza_id = $request->filled('raza_id') ? $request->raza_id : null;
        $adopcion->otra_raza = $request->filled('raza_id') ? null : ($request->filled('otra_raza') ? trim($request->otra_raza) : null);
        $adopcion->edad_anios = $request->filled('edad_anios') ? $request->edad_anios : null;
        $adopcion->sexo = $request->sexo;
        $adopcion->tamano = $request->tamano;
        $adopcion->color_predominante = $request->filled('color_predominante') ? trim($request->color_predominante) : null;
        $adopcion->descripcion = $request->descripcion;
        $adopcion->vacunas_aplicadas = $request->filled('vacunas_aplicadas') ? trim($request->vacunas_aplicadas) : null;
        $adopcion->esterilizado = $request->filled('esterilizado') ? (int) $request->esterilizado : null;
        $adopcion->condicion_salud = $request->filled('condicion_salud') ? trim($request->condicion_salud) : null;
        $adopcion->descripcion_salud = $request->filled('descripcion_salud') ? trim($request->descripcion_salud) : null;
        $adopcion->requisitos = $request->filled('requisitos') ? trim($request->requisitos) : null;
        $adopcion->colonia_barrio = $request->filled('colonia_barrio') ? trim($request->colonia_barrio) : null;
        $adopcion->calle_referencias = $request->filled('calle_referencias') ? trim($request->calle_referencias) : null;
        $adopcion->latitud = $request->filled('latitud') ? $request->latitud : null;
        $adopcion->longitud = $request->filled('longitud') ? $request->longitud : null;
        $adopcion->save();

        foreach ($fotosParaEliminar as $fotoEliminar) {
            if ($fotoEliminar->url && Storage::disk('public')->exists($fotoEliminar->url)) {
                Storage::disk('public')->delete($fotoEliminar->url);
            }
            $fotoEliminar->delete();
        }

        $fotosRestantes = $adopcion->fotos()->orderBy('orden')->get();

        foreach ($fotosRestantes as $index => $fotoRestante) {
            $nuevoOrden = $index + 1;
            if ((int) $fotoRestante->orden !== $nuevoOrden) {
                $fotoRestante->orden = $nuevoOrden;
                $fotoRestante->save();
            }
        }

        $ordenInicial = $fotosRestantes->count();

        foreach ($archivosNuevos as $index => $archivo) {
            $ruta = $archivo->store('adopciones', 'public');

            AdopcionFoto::create([
                'publicacion_id' => $adopcion->id_publicacion,
                'url' => $ruta,
                'orden' => $ordenInicial + $index + 1,
            ]);
        }

        if ($request->expectsJson()) {
            session()->flash('success', 'Se acaba de modificar tu publicación exitosamente.');

            return response()->json([
                'success' => true,
                'message' => 'Se acaba de modificar tu publicación exitosamente.',
                'redirect' => route('adopciones.mis-adopciones'),
            ]);
        }

        return redirect()
            ->route('adopciones.mis-adopciones')
            ->with('success', 'Se acaba de modificar tu publicación exitosamente.');
    }

    public function marcarAdoptada($id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);

        if ((int) $this->usuarioIdActual() !== (int) $adopcion->autor_usuario_id) {
            abort(403);
        }

        if ($adopcion->estado !== 'EN_PROCESO') {
            return redirect()
                ->route('adopciones.mis-adopciones')
                ->with('error', 'Solo puedes marcar como adoptada una publicación que esté en proceso.');
        }

        $adopcion->estado = 'ADOPTADA';
        $adopcion->save();

        return redirect()
            ->route('adopciones.mis-adopciones')
            ->with('success', 'La mascota fue marcada como adoptada correctamente.');
    }

    public function volverEnProceso($id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);

        if ((int) $this->usuarioIdActual() !== (int) $adopcion->autor_usuario_id) {
            abort(403);
        }

        if ($adopcion->estado !== 'ADOPTADA') {
            return redirect()
                ->route('adopciones.mis-adopciones')
                ->with('error', 'Solo puedes regresar a proceso una publicación que esté adoptada.');
        }

        $adopcion->estado = 'EN_PROCESO';
        $adopcion->save();

        return redirect()
            ->route('adopciones.mis-adopciones')
            ->with('success', 'La publicación volvió a estado en proceso.');
    }

    public function destroy($id)
    {
        $adopcion = PublicacionAdopcion::with('fotos')->findOrFail($id);

        if ((int) $this->usuarioIdActual() !== (int) $adopcion->autor_usuario_id) {
            abort(403);
        }

        foreach ($adopcion->fotos as $foto) {
            if ($foto->url && Storage::disk('public')->exists($foto->url)) {
                Storage::disk('public')->delete($foto->url);
            }
            $foto->delete();
        }

        $adopcion->delete();

        return redirect()
            ->route('adopciones.mis-adopciones')
            ->with('success', 'Publicación eliminada correctamente.');
    }
}