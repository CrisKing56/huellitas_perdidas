<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\ConsejoImagen;
use App\Models\Organizacion; // <-- ¡NUEVO! IMPORTANTE AGREGARLO
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsejoController extends Controller
{
    public function create()
    {
        return view('consejos.create'); 
    }

    public function store(Request $request)
    {
        // 1. Validación de datos 
        $request->validate([
            'titulo'       => 'required|max:100',
            'resumen'      => 'required|max:200',
            'categoria_id' => 'required|integer',
            'especie_id'   => 'required|integer',
            'contenido'    => 'required',
            'imagenes'     => 'nullable|array|max:3',
            'imagenes.*'   => 'image|mimes:jpg,jpeg,png|max:5120',
        ], [
            // ... (Tus mensajes de error en español que ya tenías)
            'titulo.required' => 'El título del consejo es obligatorio.',
        ]);

        // --- LA MAGIA DE LA AUTENTICACIÓN ---
        
        // A. Sacamos el ID del usuario que está logueado en este momento
        $idUsuarioLogueado = Auth::id();

        // B. Buscamos la veterinaria que le pertenece a este usuario
        $veterinaria = Organizacion::where('usuario_dueno_id', $idUsuarioLogueado)->first();

        // C. Medida de seguridad: Si un usuario "normal" intenta publicar, lo rebotamos
        if (!$veterinaria) {
            return redirect()->back()->withErrors(['No tienes una veterinaria registrada para poder publicar consejos.']);
        }

        // ------------------------------------

        // 2. Validar si es "borrador" o "publicar"
        $estado = $request->input('accion') === 'borrador' ? 'PENDIENTE' : 'APROBADO';

        // 3. Guardar el Consejo usando el ID REAL de la veterinaria
        $consejo = Consejo::create([
            'autor_organizacion_id' => $veterinaria->id_organizacion, // <-- ¡EL ID REAL DINÁMICO!
            'titulo'                => $request->titulo,
            'resumen'               => $request->resumen,
            'categoria_id'          => $request->categoria_id,
            'especie_id'            => $request->especie_id,
            'contenido'             => $request->contenido,
            'estado_publicacion'    => $estado,
        ]);

        // 4. Guardar las imágenes (Queda igual que antes)
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $ruta = $imagen->store('consejos', 'public');
                
                ConsejoImagen::create([
                    'consejo_id' => $consejo->id_consejo,
                    'url'        => $ruta,
                    'orden'      => $index + 1
                ]);
            }
        }

        $mensajeFlash = $estado === 'PENDIENTE' 
            ? 'Borrador guardado exitosamente.' 
            : '¡Consejo publicado exitosamente!';

        return redirect()->route('consejos.index')->with('success', $mensajeFlash);
    }

    public function index()
    {
        $consejos = Consejo::with(['imagenes', 'categoria'])
            ->where('estado_publicacion', 'APROBADO')
            ->orderBy('creado_en', 'desc')
            ->paginate(9);

        return view('consejos.index', compact('consejos'));
    }

    public function show($id)
    {
        // Buscamos el consejo por su ID, y cargamos sus relaciones de golpe para que sea más rápido
        $consejo = Consejo::with(['imagenes', 'organizacion', 'categoria'])->findOrFail($id);

        return view('consejos.show', compact('consejo'));
    }
}