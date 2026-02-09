<?php

namespace App\Http\Controllers;

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
        // 1. Validamos que no envíen basura
        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required', 
            'foto' => 'required|image|max:5120', // Máximo 5MB
        ]);

        // 2. Guardamos la Mascota
        // OJO: Aquí ponemos '1' manual para saltarnos el Login
        $publicacion = PublicacionExtravio::create([
            'autor_usuario_id' => 1, // <--- EL TRUCO TEMPORAL
            'nombre' => $request->nombre,
            'especie_id' => $request->especie_id,
            'color' => $request->color,
            'tamano' => $request->tamano,
            'sexo' => $request->sexo,
            'fecha_extravio' => $request->fecha_extravio,
            'colonia_barrio' => $request->colonia_barrio,
            'descripcion' => $request->descripcion,
            'estado' => 'ACTIVA'
        ]);

        // 3. Guardamos la Foto (Si subieron una)
        if ($request->hasFile('foto')) {
            // Esto guarda la imagen en: storage/app/public/extravios
            $path = $request->file('foto')->store('extravios', 'public');

            ExtravioFoto::create([
                'publicacion_id' => $publicacion->id_publicacion,
                'url' => '/storage/' . $path, // Guardamos la ruta pública
                'orden' => 1
            ]);
        }

        return redirect()->route('mascotas.index')->with('success', '¡Reporte guardado!');
    }

    // Muestra el catálogo
    public function index()
    {
    // 1. Buscamos los datos
    $mascotas = PublicacionExtravio::with('fotos')
        ->orderBy('id_publicacion', 'desc') 
        ->get();

    // 2. Llamamos a TU archivo específico 'mascotas-perdidas'
    // y le pasamos la variable con compact()
    return view('mascotas-perdidas', compact('mascotas'));
    }
}