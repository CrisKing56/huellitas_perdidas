<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\PublicacionExtravio;
use App\Models\ExtravioFoto;
use Illuminate\Http\Request;

class ExtravioController extends Controller
{
    // Muestra el formulario
    public function create()
    {
        return view('reportar-mascota');
    }

    // Guarda los datos
    public function store(Request $request)
    {
        //Validacion

        
        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required', 
            'foto' => 'required|image|max:5120',
            'colonia_barrio' => 'required' 
        ]);

        
        $rutaFoto = $request->file('foto')->store('mascotas', 'public');


        $publicacion = PublicacionExtravio::create([
            'autor_usuario_id' => Auth::id(), 
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


        
        if ($rutaFoto) {
        DB::table('extravio_fotos')->insert([
            'publicacion_id' => $publicacion->id_publicacion,
            'url' => $rutaFoto,
            'orden' => 1
        ]);
    }

        return redirect()->route('mascotas.index')->with('success', '¡Reporte guardado!');
    }

    // Muestra el catálogo
    public function index()
    {

    $mascotas = PublicacionExtravio::where('autor_usuario_id', Auth::id())
        ->with('fotos')
        ->orderBy('id_publicacion', 'desc') 
        ->paginate(10);

        return view('extravios.mis-reportes', compact('mascotas'));
    }


    
    public function index2()
    {
        $mascotas = PublicacionExtravio::with('fotoPrincipal') 
            ->where('estado', '!=', 'RESUELTA') 
            ->orderBy('id_publicacion', 'desc') 
            ->paginate(12); 

        return view('mascotas-perdidas', compact('mascotas')); 
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
            'calle_referencias' => $request->calle, 
        ]);


        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('mascotas', 'public');

            
            DB::table('fotos_publicacion')
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

        $publicacion = PublicacionExtravio::with(['autor', 'fotoPrincipal'])->findOrFail($id);

        return view('mascota-detalle', compact('publicacion'));
    }


}