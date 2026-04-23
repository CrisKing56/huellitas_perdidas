<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublicacionExtravio;
use App\Models\PublicacionAdopcion;
use App\Models\Organizacion;
use App\Models\User;

class ReporteController extends Controller
{
    /**
     * Helper central para crear PDFs sin depender del Facade Pdf.
     */
    private function makePdf(string $view, array $data = [], string $paper = 'letter', string $orientation = 'landscape')
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView($view, $data);
        $pdf->setPaper($paper, $orientation);

        return $pdf;
    }

    // ==========================================
    // 1. REPORTE DE MASCOTAS EXTRAVIADAS
    // ==========================================
    public function generarReporteMascotas(Request $request)
    {
        $query = PublicacionExtravio::query();

        if ($request->filled('q')) {
            $texto = trim($request->q);

            $query->where(function ($q) use ($texto) {
                $q->where('nombre', 'LIKE', '%' . $texto . '%')
                    ->orWhere('id_publicacion', $texto)
                    ->orWhere('colonia_barrio', 'LIKE', '%' . $texto . '%')
                    ->orWhere('calle_referencias', 'LIKE', '%' . $texto . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $mascotas = $query->orderBy('created_at', 'desc')->get();

        $pdf = $this->makePdf('admin.reportes.pdf_mascotas', compact('mascotas'));

        return $pdf->download('Reporte_Mascotas_Extraviadas.pdf');
    }

    // ==========================================
    // 2. REPORTE DE ADOPCIONES
    // ==========================================
    public function generarReporteAdopciones(Request $request)
    {
        $query = PublicacionAdopcion::query();

        if ($request->filled('q')) {
            $texto = trim($request->q);

            $query->where(function ($q) use ($texto) {
                $q->where('nombre', 'LIKE', '%' . $texto . '%')
                    ->orWhere('id_publicacion', $texto)
                    ->orWhere('colonia_barrio', 'LIKE', '%' . $texto . '%');

                // Solo si existe la relación "autor" en tu modelo
                try {
                    $q->orWhereHas('autor', function ($queryAutor) use ($texto) {
                        $queryAutor->where('nombre', 'LIKE', '%' . $texto . '%');
                    });
                } catch (\Throwable $e) {
                    // Evita romper si la relación no está definida correctamente
                }
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $adopciones = $query->orderBy('created_at', 'desc')->get();

        $pdf = $this->makePdf('admin.reportes.pdf_adopciones', compact('adopciones'));

        return $pdf->download('Reporte_Adopciones_Huellitas.pdf');
    }

    // ==========================================
    // 3. REPORTE DE VETERINARIAS
    // ==========================================
    public function generarReporteVeterinarias(Request $request)
    {
        $query = Organizacion::query()->where('tipo', 'VETERINARIA');

        if ($request->filled('q')) {
            $texto = trim($request->q);
            $query->where('nombre', 'LIKE', '%' . $texto . '%');
        }

        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        $veterinarias = $query->orderBy('created_at', 'desc')->get();

        $pdf = $this->makePdf('admin.reportes.pdf_veterinarias', compact('veterinarias'));

        return $pdf->download('Reporte_Veterinarias_Huellitas.pdf');
    }

    // ==========================================
    // 4. REPORTE DE REFUGIOS
    // ==========================================
    public function generarReporteRefugios(Request $request)
    {
        $query = Organizacion::query()->where('tipo', 'REFUGIO');

        if ($request->filled('q')) {
            $texto = trim($request->q);
            $query->where('nombre', 'LIKE', '%' . $texto . '%');
        }

        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        $refugios = $query->orderBy('created_at', 'desc')->get();

        $pdf = $this->makePdf('admin.reportes.pdf_refugios', compact('refugios'));

        return $pdf->download('Reporte_Refugios_Huellitas.pdf');
    }

    // ==========================================
    // 5. REPORTE DE USUARIOS
    // ==========================================
    public function generarReporteUsuarios(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $texto = trim($request->q);

            $query->where(function ($q) use ($texto) {
                $q->where('nombre', 'LIKE', '%' . $texto . '%')
                    ->orWhere('correo', 'LIKE', '%' . $texto . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $texto . '%')
                    ->orWhere('id_usuario', $texto);
            });
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->orderBy('created_at', 'desc')->get();

        $pdf = $this->makePdf('admin.reportes.pdf_usuarios', compact('usuarios'));

        return $pdf->download('Reporte_Usuarios_Huellitas.pdf');
    }
}