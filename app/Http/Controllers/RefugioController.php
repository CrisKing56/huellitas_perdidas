<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;

class RefugioController extends Controller
{
    // Muestra el catálogo de refugios
    public function index()
    {
        $refugios = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->where('estado_revision', 'APROBADA') // Solo mostramos los aprobados
            ->orderBy('creado_en', 'desc')
            ->paginate(9);

        return view('refugios.index', compact('refugios'));
    }

    // Muestra el perfil individual de un refugio
    public function show($id)
    {
        $refugio = Organizacion::with(['direccion', 'fotos', 'refugioDetalle'])
            ->where('tipo', 'REFUGIO')
            ->findOrFail($id);

        return view('refugios.show', compact('refugio'));
    }
}