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
    // Muestra el formulario
    public function create()
    {
        return view('reportar-mascota');
    }

    // Guarda los datos
    public function store(Request $request)
    {
        // 1. Validación estricta
        $request->validate([
            'nombre' => 'required|string',
            'especie_id' => 'required', 
            'foto' => 'required|image|max:5120',
            'colonia_barrio' => 'required',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric'
        ]);

        try {
            // Iniciamos la transacción para asegurar que todo se guarde o nada se guarde
            DB::transaction(function () use ($request) {
                
                // 2. CREAR LA UBICACIÓN PRIMERO
                $nuevaUbicacion = Ubicacion::create([
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'precision_metros' => null 
                ]);

                // 3. CREAR LA PUBLICACIÓN DE EXTRAVÍO
                // Usamos create() y le pasamos el ID de la ubicación recién creada
                $publicacion = PublicacionExtravio::create([
                    'autor_usuario_id' => Auth::id(),
                    'ubicacion_id' => $nuevaUbicacion->id_ubicacion, // <-- Enlace crucial
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

                // 4. GUARDAR LA FOTO
                if ($request->hasFile('foto')) {
                    $rutaFoto = $request->file('foto')->store('mascotas', 'public');

                    DB::table('extravio_fotos')->insert([
                        'publicacion_id' => $publicacion->id_publicacion, // Usamos el ID de la publicación que acabamos de crear
                        'url' => $rutaFoto,
                        'orden' => 1
                    ]);
                }

                // Nota sobre los usuarios: No necesitas insertar datos en la tabla usuarios aquí.
                // El autor_usuario_id ya vincula esta publicación con el usuario que inició sesión.
                // Podrás acceder a sus datos en la vista usando $publicacion->autor->nombre.
            });

            return redirect()->route('mascotas.index2')->with('success', '¡Reporte guardado exitosamente!');

        } catch (\Exception $e) {
            // Si algo falla, Laravel detiene todo, no guarda medias tablas, y muestra este error.
            return back()->withInput()->withErrors(['error' => 'Hubo un problema al guardar el reporte: ' . $e->getMessage()]);
        }
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
            // Sería ideal validar la latitud y longitud aquí también si permites editar el mapa
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
            'calle_referencias' => $request->calle_referencias, // Corregido: antes decía $request->calle
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

    $comentarios = DB::table('comentarios_extravio as c')
        ->join('usuarios as u', 'u.id_usuario', '=', 'c.usuario_id')
        ->where('c.publicacion_id', $id)
        ->whereNull('c.comentario_padre_id')
        ->where('c.estado', 'VISIBLE')
        ->select(
            'c.id_comentario',
            'c.comentario',
            'c.creado_en',
            'u.nombre as usuario_nombre',
            DB::raw('NULL as usuario_foto')
        )
        ->orderBy('c.creado_en', 'desc')
        ->get();

    return view('mascota-detalle', compact('publicacion', 'comentarios'));
}

    public function storeComment(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::findOrFail($id);

        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        DB::table('comentarios_extravio')->insert([
            'publicacion_id' => $publicacion->id_publicacion,
            'usuario_id' => Auth::id(),
            'comentario_padre_id' => null,
            'comentario' => trim($request->comentario),
            'estado' => 'VISIBLE',
            'creado_en' => now(),
            'actualizado_en' => now(),
        ]);

        return redirect()
            ->route('extravios.show', $publicacion->id_publicacion)
            ->with('success', 'Comentario publicado correctamente.');
    }
}