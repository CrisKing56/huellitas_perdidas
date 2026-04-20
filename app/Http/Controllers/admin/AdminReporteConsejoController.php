<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReporteConsejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReporteConsejoController extends Controller
{
    private function usuarioAdminId()
    {
        return Auth::user()->id_usuario ?? null;
    }

    public function index(Request $request)
    {
        $filtros = [
            'estado' => (string) $request->get('estado', ''),
            'q' => trim((string) $request->get('q', '')),
        ];

        $query = ReporteConsejo::with([
            'consejo.organizacion',
            'usuarioReporta',
            'revisor',
        ]);

        if ($filtros['estado'] !== '') {
            $query->where('estado', $filtros['estado']);
        }

        if ($filtros['q'] !== '') {
            $query->where(function ($sub) use ($filtros) {
                $sub->where('motivo', 'like', '%' . $filtros['q'] . '%')
                    ->orWhereHas('consejo', function ($q) use ($filtros) {
                        $q->where('titulo', 'like', '%' . $filtros['q'] . '%');
                    })
                    ->orWhereHas('usuarioReporta', function ($q) use ($filtros) {
                        $q->where('nombre', 'like', '%' . $filtros['q'] . '%')
                          ->orWhere('correo', 'like', '%' . $filtros['q'] . '%');
                    });
            });
        }

        $reportes = $query
            ->orderByRaw("CASE 
                WHEN estado = 'ABIERTO' THEN 1
                WHEN estado = 'EN_REVISION' THEN 2
                WHEN estado = 'RESUELTO' THEN 3
                WHEN estado = 'DESCARTADO' THEN 4
                ELSE 5
            END")
            ->orderBy('creado_en', 'desc')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'abiertos' => ReporteConsejo::where('estado', 'ABIERTO')->count(),
            'en_revision' => ReporteConsejo::where('estado', 'EN_REVISION')->count(),
            'resueltos' => ReporteConsejo::where('estado', 'RESUELTO')->count(),
            'descartados' => ReporteConsejo::where('estado', 'DESCARTADO')->count(),
        ];

        return view('admin.reportes-consejos.index', compact('reportes', 'stats', 'filtros'));
    }

    public function show($id)
    {
        $reporte = ReporteConsejo::with([
            'consejo.imagenes',
            'consejo.categoria',
            'consejo.organizacion',
            'usuarioReporta',
            'revisor',
        ])->findOrFail($id);

        return view('admin.reportes-consejos.show', compact('reporte'));
    }

    public function marcarEnRevision($id)
    {
        $reporte = ReporteConsejo::findOrFail($id);

        $reporte->estado = 'EN_REVISION';
        $reporte->revisado_por = $this->usuarioAdminId();
        $reporte->revisado_en = now();
        $reporte->save();

        return redirect()
            ->route('admin.reportes-consejos.show', $reporte->id_reporte)
            ->with('success', 'El reporte fue marcado como en revisión.');
    }

    public function resolver(Request $request, $id)
    {
        $request->validate([
            'estado' => ['required', 'in:RESUELTO,DESCARTADO'],
            'accion_tomada' => ['required', 'string', 'max:120'],
            'motivo_resolucion' => ['required', 'string', 'max:255'],
        ], [
            'estado.required' => 'Debes seleccionar el resultado del reporte.',
            'estado.in' => 'El resultado seleccionado no es válido.',
            'accion_tomada.required' => 'Debes escribir la acción tomada.',
            'accion_tomada.max' => 'La acción tomada no debe exceder 120 caracteres.',
            'motivo_resolucion.required' => 'Debes escribir el motivo de resolución.',
            'motivo_resolucion.max' => 'El motivo de resolución no debe exceder 255 caracteres.',
        ]);

        $reporte = ReporteConsejo::findOrFail($id);

        $reporte->estado = $request->estado;
        $reporte->revisado_por = $this->usuarioAdminId();
        $reporte->revisado_en = now();
        $reporte->accion_tomada = trim($request->accion_tomada);
        $reporte->motivo_resolucion = trim($request->motivo_resolucion);
        $reporte->save();

        return redirect()
            ->route('admin.reportes-consejos.show', $reporte->id_reporte)
            ->with('success', 'El reporte fue actualizado correctamente.');
    }
}