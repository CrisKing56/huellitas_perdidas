<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion; 
use App\Models\Organizacion;
use App\Models\User;


class ReporteController extends Controller
{
    // ==========================================
    // 1. REPORTE DE MASCOTAS EXTRAVIADAS
    // ==========================================
    public function generarReporteMascotas(Request $request)
    {
        $query = PublicacionExtravio::query();

        // Filtro de búsqueda general (q)
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('id_publicacion', $request->q)
                  ->orWhere('colonia_barrio', 'LIKE', '%' . $request->q . '%');
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        $mascotas = $query->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.reportes.pdf_mascotas', compact('mascotas'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_Mascotas_Extraviadas.pdf');
    }

    // ==========================================
    // 2. REPORTE DE ADOPCIONES
    // ==========================================
    public function generarReporteAdopciones(Request $request)
    {
        $query = PublicacionAdopcion::query();

        // Filtro de búsqueda general (q)
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('id_publicacion', $request->q)
                  ->orWhereHas('autor', function($queryAutor) use ($request) {
                      $queryAutor->where('nombre', 'LIKE', '%' . $request->q . '%');
                  })
                  ->orWhere('colonia_barrio', 'LIKE', '%' . $request->q . '%');
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $adopciones = $query->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.reportes.pdf_adopciones', compact('adopciones'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_Adopciones_Huellitas.pdf');
    }

    // ==========================================
    // 3. REPORTE DE VETERINARIAS
    // ==========================================
    public function generarReporteVeterinarias(Request $request)
    {
        // Filtro nativo para traer solo Veterinarias (Columna corregida)
        $query = Organizacion::query()->where('tipo', 'VETERINARIA');

        // Filtro de búsqueda simple (solo por nombre para evitar el error de columnas ajenas)
        if ($request->filled('q')) {
            $query->where('nombre', 'LIKE', '%' . $request->q . '%');
        }

        // Filtro por Estado de Revisión
        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        $veterinarias = $query->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.reportes.pdf_veterinarias', compact('veterinarias'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_Veterinarias_Huellitas.pdf');
    }

    // ==========================================
    // 4. REPORTE DE REFUGIOS
    // ==========================================
    public function generarReporteRefugios(Request $request)
    {
        $query = Organizacion::query()->where('tipo', 'REFUGIO');

        if ($request->filled('q')) {
            $query->where('nombre', 'LIKE', '%' . $request->q . '%');
        }

        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        $refugios = $query->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.reportes.pdf_refugios', compact('refugios'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_Refugios_Huellitas.pdf');
    }

    // ==========================================
    // 5. REPORTE DE USUARIOS
    // ==========================================
    public function generarReporteUsuarios(Request $request)
    {
        $query = User::query();

        // Filtro de búsqueda (ID, nombre o correo)
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('correo', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('id_usuario', $request->q);
            });
        }

        // Filtro por Rol (ADMIN, USUARIO, etc)
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        // Filtro por Estado (ACTIVA, SUSPENDIDA)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->orderBy('created_at', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.reportes.pdf_usuarios', compact('usuarios'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('Reporte_Usuarios_Huellitas.pdf');
    }
}