<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\ConsejoCategoria;
use App\Models\ConsejoImagen;
use App\Models\Especie;
use App\Models\Etiqueta;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConsejoController extends Controller
{
    private function usuarioIdActual()
    {
        return Auth::user()->id_usuario ?? null;
    }

    private function organizacionActual()
    {
        $usuarioId = $this->usuarioIdActual();

        if (!$usuarioId) {
            return null;
        }

        return Organizacion::where('usuario_dueno_id', $usuarioId)
            ->whereIn('tipo', ['VETERINARIA', 'REFUGIO'])
            ->where('estado_revision', 'APROBADA')
            ->first();
    }

    private function correoDestinoOrganizacion(?Organizacion $organizacion): ?string
    {
        if (!$organizacion || !$organizacion->usuarioDueno) {
            return null;
        }

        return $organizacion->usuarioDueno->correo ?? $organizacion->usuarioDueno->email ?? null;
    }

    private function enviarCorreoEnRevision(Consejo $consejo, string $tipoAccion = 'creado'): void
    {
        try {
            $consejo->loadMissing(['organizacion.usuarioDueno', 'categoria', 'especie']);

            $correoDestino = $this->correoDestinoOrganizacion($consejo->organizacion);

            if (!$correoDestino) {
                return;
            }

            $html = view('emails.consejo-enviado-revision', [
                'consejo' => $consejo,
                'tipoAccion' => $tipoAccion,
                'urlMisConsejos' => route('consejos.mis-consejos'),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $consejo, $tipoAccion) {
                $asunto = $tipoAccion === 'actualizado'
                    ? 'Tu consejo fue actualizado y enviado nuevamente a revisión'
                    : 'Tu consejo fue enviado a revisión';

                $message->to($correoDestino)
                    ->subject($asunto . ': ' . $consejo->titulo);
            });
        } catch (\Throwable $e) {
        }
    }

    private function reordenarImagenes(Consejo $consejo): void
    {
        $imagenes = $consejo->imagenes()->orderBy('orden')->orderBy('id_imagen')->get();

        foreach ($imagenes as $index => $imagen) {
            $nuevoOrden = $index + 1;

            if ((int) $imagen->orden !== $nuevoOrden) {
                $imagen->orden = $nuevoOrden;
                $imagen->save();
            }
        }
    }

    private function puedeVerNoPublicado(Consejo $consejo): bool
    {
        $organizacionActual = $this->organizacionActual();

        return $organizacionActual
            && (int) $organizacionActual->id_organizacion === (int) $consejo->autor_organizacion_id;
    }

    public function index(Request $request)
    {
        $filtros = [
            'q' => trim((string) $request->get('q', '')),
            'categoria' => (string) $request->get('categoria', ''),
            'especie' => (string) $request->get('especie', ''),
            'etiqueta' => (string) $request->get('etiqueta', ''),
        ];

        $categorias = ConsejoCategoria::orderBy('nombre')->get();
        $especies = Especie::where('activo', 1)->orderBy('nombre')->get();
        $etiquetas = Etiqueta::where('activo', 1)->orderBy('nombre')->get();

        $organizacionActual = Auth::check() ? $this->organizacionActual() : null;
        $puedePublicarConsejo = $organizacionActual !== null;

        $consejos = Consejo::with(['imagenes', 'categoria', 'organizacion', 'etiquetas'])
            ->where('estado_publicacion', 'APROBADO')
            ->when($filtros['q'] !== '', function ($query) use ($filtros) {
                $query->where(function ($sub) use ($filtros) {
                    $sub->where('titulo', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('resumen', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('contenido', 'like', '%' . $filtros['q'] . '%')
                        ->orWhereHas('organizacion', function ($q) use ($filtros) {
                            $q->where('nombre', 'like', '%' . $filtros['q'] . '%');
                        });
                });
            })
            ->when($filtros['categoria'] !== '', function ($query) use ($filtros) {
                $query->where('categoria_id', $filtros['categoria']);
            })
            ->when($filtros['especie'] !== '', function ($query) use ($filtros) {
                $query->where('especie_id', $filtros['especie']);
            })
            ->when($filtros['etiqueta'] !== '', function ($query) use ($filtros) {
                $query->whereHas('etiquetas', function ($q) use ($filtros) {
                    $q->where('etiquetas.id_etiqueta', $filtros['etiqueta']);
                });
            })
            ->orderBy('creado_en', 'desc')
            ->paginate(9)
            ->withQueryString();

        return view('consejos.index', compact(
            'consejos',
            'categorias',
            'especies',
            'etiquetas',
            'filtros',
            'puedePublicarConsejo'
        ));
    }

    public function show($id)
    {
        $consejo = Consejo::with([
                'imagenes',
                'organizacion',
                'categoria',
                'especie',
                'etiquetas',
            ])
            ->findOrFail($id);

        if ($consejo->estado_publicacion !== 'APROBADO' && !$this->puedeVerNoPublicado($consejo)) {
            abort(404);
        }

        $consejosRelacionados = Consejo::with(['imagenes', 'categoria', 'organizacion'])
            ->where('estado_publicacion', 'APROBADO')
            ->where('id_consejo', '!=', $consejo->id_consejo)
            ->where(function ($query) use ($consejo) {
                $query->where('categoria_id', $consejo->categoria_id)
                    ->orWhere('especie_id', $consejo->especie_id);
            })
            ->orderBy('creado_en', 'desc')
            ->take(3)
            ->get();

        return view('consejos.show', compact('consejo', 'consejosRelacionados'));
    }

    public function create()
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'Solo veterinarias y refugios aprobados pueden publicar consejos.');
        }

        $categorias = ConsejoCategoria::orderBy('nombre')->get();
        $especies = Especie::where('activo', 1)->orderBy('nombre')->get();
        $etiquetas = Etiqueta::where('activo', 1)->orderBy('nombre')->get();

        return view('consejos.create', compact('organizacion', 'categorias', 'especies', 'etiquetas'));
    }

    public function store(Request $request)
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'Solo veterinarias y refugios aprobados pueden publicar consejos.');
        }

        $request->validate([
            'titulo' => ['required', 'string', 'max:100'],
            'resumen' => ['required', 'string', 'max:200'],
            'categoria_id' => ['required', 'integer', Rule::exists('categorias_consejo', 'id_categoria')],
            'especie_id' => ['required', 'integer', Rule::exists('especies', 'id_especie')],
            'contenido' => ['required', 'string'],
            'etiquetas' => ['nullable', 'array'],
            'etiquetas.*' => ['integer', Rule::exists('etiquetas', 'id_etiqueta')],
            'imagenes' => ['nullable', 'array', 'max:3'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'titulo.required' => 'El título del consejo es obligatorio.',
            'resumen.required' => 'El resumen es obligatorio.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'especie_id.required' => 'Debes seleccionar una especie.',
            'contenido.required' => 'El contenido del consejo es obligatorio.',
            'imagenes.max' => 'Solo puedes subir hasta 3 imágenes.',
            'imagenes.*.image' => 'Cada archivo debe ser una imagen válida.',
            'imagenes.*.mimes' => 'Las imágenes deben ser JPG o PNG.',
            'imagenes.*.max' => 'Cada imagen puede pesar máximo 5 MB.',
        ]);

        $consejo = Consejo::create([
            'autor_organizacion_id' => $organizacion->id_organizacion,
            'titulo' => trim($request->titulo),
            'resumen' => trim($request->resumen),
            'categoria_id' => $request->categoria_id,
            'especie_id' => $request->especie_id,
            'contenido' => trim($request->contenido),
            'estado_publicacion' => 'PENDIENTE',
            'revisado_por' => null,
            'revisado_en' => null,
            'motivo_rechazo' => null,
        ]);

        if ($request->filled('etiquetas')) {
            $consejo->etiquetas()->sync($request->etiquetas);
        }

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $ruta = $imagen->store('consejos', 'public');

                ConsejoImagen::create([
                    'consejo_id' => $consejo->id_consejo,
                    'url' => $ruta,
                    'orden' => $index + 1,
                ]);
            }
        }

        $this->enviarCorreoEnRevision($consejo, 'creado');

        return redirect()
            ->route('consejos.mis-consejos')
            ->with('success', 'Tu consejo fue enviado a revisión correctamente.');
    }

    public function misConsejos()
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'No tienes una organización aprobada para gestionar consejos.');
        }

        $baseQuery = Consejo::with(['imagenes', 'categoria', 'especie', 'etiquetas'])
            ->where('autor_organizacion_id', $organizacion->id_organizacion);

        $consejos = (clone $baseQuery)
            ->orderBy('creado_en', 'desc')
            ->paginate(10);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pendientes' => (clone $baseQuery)->where('estado_publicacion', 'PENDIENTE')->count(),
            'aprobados' => (clone $baseQuery)->where('estado_publicacion', 'APROBADO')->count(),
            'rechazados' => (clone $baseQuery)->where('estado_publicacion', 'RECHAZADO')->count(),
        ];

        return view('consejos.mis-consejos', compact('organizacion', 'consejos', 'stats'));
    }

    public function edit($id)
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'No tienes permisos para editar consejos.');
        }

        $consejo = Consejo::with(['imagenes', 'etiquetas'])
            ->where('autor_organizacion_id', $organizacion->id_organizacion)
            ->findOrFail($id);

        $categorias = ConsejoCategoria::orderBy('nombre')->get();
        $especies = Especie::where('activo', 1)->orderBy('nombre')->get();
        $etiquetas = Etiqueta::where('activo', 1)->orderBy('nombre')->get();

        return view('consejos.edit', compact('organizacion', 'consejo', 'categorias', 'especies', 'etiquetas'));
    }

    public function update(Request $request, $id)
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'No tienes permisos para actualizar consejos.');
        }

        $consejo = Consejo::with(['imagenes', 'etiquetas'])
            ->where('autor_organizacion_id', $organizacion->id_organizacion)
            ->findOrFail($id);

        $request->validate([
            'titulo' => ['required', 'string', 'max:100'],
            'resumen' => ['required', 'string', 'max:200'],
            'categoria_id' => ['required', 'integer', Rule::exists('categorias_consejo', 'id_categoria')],
            'especie_id' => ['required', 'integer', Rule::exists('especies', 'id_especie')],
            'contenido' => ['required', 'string'],
            'etiquetas' => ['nullable', 'array'],
            'etiquetas.*' => ['integer', Rule::exists('etiquetas', 'id_etiqueta')],
            'eliminar_imagenes' => ['nullable', 'array'],
            'eliminar_imagenes.*' => ['integer'],
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'titulo.required' => 'El título del consejo es obligatorio.',
            'resumen.required' => 'El resumen es obligatorio.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'especie_id.required' => 'Debes seleccionar una especie.',
            'contenido.required' => 'El contenido del consejo es obligatorio.',
            'imagenes.*.image' => 'Cada archivo debe ser una imagen válida.',
            'imagenes.*.mimes' => 'Las imágenes deben ser JPG o PNG.',
            'imagenes.*.max' => 'Cada imagen puede pesar máximo 5 MB.',
        ]);

        $idsEliminar = collect($request->input('eliminar_imagenes', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        $imagenesAEliminar = $consejo->imagenes->whereIn('id_imagen', $idsEliminar);
        $totalActuales = $consejo->imagenes->count();
        $totalNuevas = count($request->file('imagenes', []));
        $totalFinal = $totalActuales - $imagenesAEliminar->count() + $totalNuevas;

        if ($totalFinal > 3) {
            return back()
                ->withErrors(['imagenes' => 'Solo puedes tener hasta 3 imágenes por consejo.'])
                ->withInput();
        }

        $consejo->update([
            'titulo' => trim($request->titulo),
            'resumen' => trim($request->resumen),
            'categoria_id' => $request->categoria_id,
            'especie_id' => $request->especie_id,
            'contenido' => trim($request->contenido),
            'estado_publicacion' => 'PENDIENTE',
            'revisado_por' => null,
            'revisado_en' => null,
            'motivo_rechazo' => null,
        ]);

        $consejo->etiquetas()->sync($request->input('etiquetas', []));

        foreach ($imagenesAEliminar as $imagen) {
            if ($imagen->url && Storage::disk('public')->exists($imagen->url)) {
                Storage::disk('public')->delete($imagen->url);
            }
            $imagen->delete();
        }

        $ultimoOrden = $consejo->imagenes()->max('orden') ?? 0;

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagenNueva) {
                $ruta = $imagenNueva->store('consejos', 'public');

                ConsejoImagen::create([
                    'consejo_id' => $consejo->id_consejo,
                    'url' => $ruta,
                    'orden' => $ultimoOrden + $index + 1,
                ]);
            }
        }

        $this->reordenarImagenes($consejo);
        $this->enviarCorreoEnRevision($consejo, 'actualizado');

        return redirect()
            ->route('consejos.mis-consejos')
            ->with('success', 'El consejo fue actualizado y enviado nuevamente a revisión.');
    }

    public function destroy($id)
    {
        $organizacion = $this->organizacionActual();

        if (!$organizacion) {
            return redirect()
                ->route('consejos.index')
                ->with('error', 'No tienes permisos para eliminar consejos.');
        }

        $consejo = Consejo::with(['imagenes'])
            ->where('autor_organizacion_id', $organizacion->id_organizacion)
            ->findOrFail($id);

        foreach ($consejo->imagenes as $imagen) {
            if ($imagen->url && Storage::disk('public')->exists($imagen->url)) {
                Storage::disk('public')->delete($imagen->url);
            }

            $imagen->delete();
        }

        $consejo->etiquetas()->sync([]);
        $consejo->delete();

        return redirect()
            ->route('consejos.mis-consejos')
            ->with('success', 'El consejo fue eliminado correctamente.');
    }
}