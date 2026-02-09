<?php

namespace App\Http\Controllers;
use App\Models\AdopcionFoto;
use App\Models\PublicacionAdopcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdopcionController extends Controller
{
    public function index()
{
    $adopciones = PublicacionAdopcion::with('fotoPrincipal')
        ->where('estado', 'DISPONIBLE')
        ->orderBy('creado_en', 'desc') 
        ->paginate(12);

    return view('mascotas-adopcion', compact('adopciones'));
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
        
        // Mapeo exacto de campos del SQL
        $adopcion->nombre = $request->nombre;
        $adopcion->especie_id = $request->especie_id;
        $adopcion->raza_id = $request->raza_id;
        $adopcion->otra_raza = $request->otra_raza;
        $adopcion->edad_anios = $request->edad_anios;
        $adopcion->sexo = $request->sexo; 
        $adopcion->tamano = $request->tamano; 
        $adopcion->color_predominante = $request->color_predominante; 
        $adopcion->descripcion = $request->descripcion;
        
        // Campos médicos específicos de adopción
        $adopcion->vacunas_aplicadas = $request->vacunas_aplicadas;

        $adopcion->esterilizado = $request->input('esterilizado');        $adopcion->condicion_salud = $request->condicion_salud;
        $adopcion->descripcion_salud = $request->descripcion_salud;
        $adopcion->requisitos = $request->requisitos;
        
        // Ubicación
        $adopcion->colonia_barrio = $request->colonia_barrio;
        $adopcion->calle_referencias = $request->calle_referencias;
        $adopcion->estado = 'DISPONIBLE';

        $adopcion->save();

        // Guardar Foto 
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('adopciones', 'public');
            
            $foto = new AdopcionFoto();
            $foto->publicacion_id = $adopcion->id_publicacion;
            $foto->url = $path;
            $foto->orden = 1; 
            $foto->save();
        }

        return redirect()->route('adopciones.mis-adopciones')->with('success', 'Mascota publicada para adopción correctamente.');
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

    //ELIMINAR 
    public function destroy($id)
    {
        $adopcion = PublicacionAdopcion::findOrFail($id);
        
        if (Auth::id() != $adopcion->autor_usuario_id) {
            abort(403);
        }

        //cambiar estado
        $adopcion->estado = 'ELIMINADA'; // [cite: 381]
        $adopcion->save();

        // Borrado físico
        // $adopcion->delete();

        return redirect()->route('adopciones.mis-adopciones')->with('success', 'Publicación eliminada.');
    }
}