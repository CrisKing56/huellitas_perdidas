<?php

namespace App\Http\Controllers;
use App\Models\AdopcionFoto;
use App\Models\PublicacionAdopcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdopcionController extends Controller
{
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

        $query = PublicacionAdopcion::with('fotoPrincipal')
            ->where('estado', 'DISPONIBLE')
            ->when($filtros['q'] !== '', function ($q) use ($filtros) {
                $q->where(function ($sub) use ($filtros) {
                    $sub->where('nombre', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('descripcion', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('colonia_barrio', 'like', '%' . $filtros['q'] . '%')
                        ->orWhere('otra_raza', 'like', '%' . $filtros['q'] . '%');
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
                $query->orderBy('creado_en', 'asc');
                break;
            case 'nombre_az':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_za':
                $query->orderBy('nombre', 'desc');
                break;
            default:
                $query->orderBy('creado_en', 'desc');
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
        $adopciones = PublicacionAdopcion::with('fotoPrincipal')
            ->where('autor_usuario_id', Auth::id())
            ->orderBy('creado_en', 'desc') 
            ->paginate(10);

        return view('adopciones.mis-adopciones', compact('adopciones'));
    }

    public function create()
    {
        return view('publicar-adopcion');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100', 
            'especie_id' => 'required|integer',
            'edad_anios' => 'nullable|integer|min:0|max:30', 
            'colonia_barrio' => 'required|string|max:120',
            'foto' => 'required|image|max:2048' 
        ]);

        $adopcion = new PublicacionAdopcion();
        $adopcion->autor_usuario_id = Auth::id(); 
        
        $adopcion->nombre = $request->nombre;
        $adopcion->especie_id = $request->especie_id;
        $adopcion->raza_id = $request->raza_id;
        $adopcion->otra_raza = $request->otra_raza;
        $adopcion->edad_anios = $request->edad_anios;
        $adopcion->sexo = $request->sexo; 
        $adopcion->tamano = $request->tamano; 
        $adopcion->color_predominante = $request->color_predominante; 
        $adopcion->descripcion = $request->descripcion;
        
        $adopcion->vacunas_aplicadas = $request->vacunas_aplicadas;

        $adopcion->esterilizado = $request->input('esterilizado');
        $adopcion->condicion_salud = $request->condicion_salud;
        $adopcion->descripcion_salud = $request->descripcion_salud;
        $adopcion->requisitos = $request->requisitos;
        
        $adopcion->colonia_barrio = $request->colonia_barrio;
        $adopcion->calle_referencias = $request->calle_referencias;
        $adopcion->estado = 'DISPONIBLE';

        $adopcion->save();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('adopciones', 'public');
            
            $foto = new AdopcionFoto();
            $foto->publicacion_id = $adopcion->id_publicacion;
            $foto->url = $path;
            $foto->orden = 1; 
            $foto->save();
        }

        return redirect()->route('adopciones.index')->with('success', 'Mascota publicada para adopción correctamente.');
    }

    public function show($id)
    {
        $adopcion = PublicacionAdopcion::with(['autor', 'fotoPrincipal'])->findOrFail($id);
        return view('adopciones.show', compact('adopcion'));
    }

    public function edit($id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);
        
        if (Auth::id() != $adopcion->autor_usuario_id) {
            return redirect()->route('adopciones.mis-adopciones');
        }

        return view('adopciones.edit', compact('adopcion'));
    }

    public function update(Request $request, $id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);

        if (Auth::id() != $adopcion->autor_usuario_id) {
            abort(403);
        }

        $adopcion->update($request->except(['foto', '_token', '_method']));

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('adopciones', 'public');
            $foto = new AdopcionFoto();
            $foto->publicacion_id = $adopcion->id_publicacion;
            $foto->url = $path;
            $foto->orden = 1; 
            $foto->save();
        }

        return redirect()->route('adopciones.mis-adopciones')->with('success', 'Publicación actualizada.');
    }

    public function destroy($id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);
        
        if (Auth::id() != $adopcion->autor_usuario_id) {
            abort(403);
        }

        $adopcion->estado = 'ELIMINADA'; 
        $adopcion->save();

        return redirect()->route('adopciones.mis-adopciones')->with('success', 'Publicación eliminada.');
    }
}