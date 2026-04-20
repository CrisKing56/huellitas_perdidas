<?php

namespace App\Http\Controllers;

use App\Models\Consejo;
use App\Models\ReporteConsejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteConsejoController extends Controller
{
    private function usuarioIdActual()
    {
        return Auth::user()->id_usuario ?? null;
    }

    public function store(Request $request, $id)
    {
        $consejo = Consejo::with('organizacion')->findOrFail($id);

        if ($consejo->estado_publicacion !== 'APROBADO') {
            return redirect()
                ->route('consejos.show', $consejo->id_consejo)
                ->with('error', 'Solo puedes reportar consejos publicados.');
        }

        $request->validate([
            'motivo' => ['required', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ], [
            'motivo.required' => 'Debes seleccionar un motivo de reporte.',
            'motivo.max' => 'El motivo no debe exceder 80 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        $usuarioId = $this->usuarioIdActual();

        $organizacionAutor = $consejo->organizacion;
        $usuarioDuenoConsejo = $organizacionAutor?->usuario_dueno_id;

        if ((int) $usuarioId === (int) $usuarioDuenoConsejo) {
            return redirect()
                ->route('consejos.show', $consejo->id_consejo)
                ->with('error', 'No puedes reportar tu propio consejo.');
        }

        $reporteActivo = ReporteConsejo::where('consejo_id', $consejo->id_consejo)
            ->where('usuario_reporta_id', $usuarioId)
            ->whereIn('estado', ['ABIERTO', 'EN_REVISION'])
            ->first();

        if ($reporteActivo) {
            return redirect()
                ->route('consejos.show', $consejo->id_consejo)
                ->with('error', 'Ya tienes un reporte activo para este consejo.');
        }

        ReporteConsejo::create([
            'consejo_id' => $consejo->id_consejo,
            'usuario_reporta_id' => $usuarioId,
            'motivo' => trim($request->motivo),
            'descripcion' => $request->filled('descripcion') ? trim($request->descripcion) : null,
            'estado' => 'ABIERTO',
        ]);

        return redirect()
            ->route('consejos.show', $consejo->id_consejo)
            ->with('success', 'El reporte fue enviado correctamente y será revisado por un administrador.');
    }
}