<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminReporteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $estado = (string) $request->get('estado', '');

        $query = DB::table('reportes as r')
            ->leftJoin('usuarios as ur', 'ur.id_usuario', '=', 'r.reportante_usuario_id')
            ->leftJoin('motivos_reporte as mr', 'mr.id_motivo', '=', 'r.motivo_id')
            ->leftJoin('publicaciones_extravio as pe', function ($join) {
                $join->on('pe.id_publicacion', '=', 'r.objetivo_id')
                    ->where('r.objetivo_tipo', '=', 'PUB_EXTRAVIO');
            })
            ->leftJoin('usuarios as ud', 'ud.id_usuario', '=', 'pe.autor_usuario_id')
            ->where('r.objetivo_tipo', 'PUB_EXTRAVIO')
            ->select(
                'r.id_reporte',
                'r.objetivo_id',
                'r.estado',
                'r.descripcion_adicional',
                'r.nota_resolucion',
                'r.creado_en',
                'r.revisado_en',
                'ur.nombre as reportante_nombre',
                'ur.correo as reportante_correo',
                'mr.nombre as motivo_nombre',
                'pe.nombre as mascota_nombre',
                'pe.colonia_barrio',
                'pe.estado as estado_publicacion',
                'ud.nombre as dueno_nombre',
                'ud.correo as dueno_correo'
            );

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('pe.nombre', 'like', "%{$q}%")
                    ->orWhere('ur.nombre', 'like', "%{$q}%")
                    ->orWhere('ur.correo', 'like', "%{$q}%")
                    ->orWhere('ud.nombre', 'like', "%{$q}%")
                    ->orWhere('ud.correo', 'like', "%{$q}%")
                    ->orWhere('mr.nombre', 'like', "%{$q}%")
                    ->orWhere('r.id_reporte', $q);
            });
        }

        if ($estado !== '') {
            $query->where('r.estado', $estado);
        }

        $reportes = $query
            ->orderByRaw("
                CASE r.estado
                    WHEN 'ENVIADO' THEN 1
                    WHEN 'EN_REVISION' THEN 2
                    WHEN 'RESUELTO' THEN 3
                    WHEN 'DESESTIMADO' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('r.creado_en')
            ->paginate(12)
            ->withQueryString();

        $baseConteo = DB::table('reportes')
            ->where('objetivo_tipo', 'PUB_EXTRAVIO');

        $stats = [
            'total' => (clone $baseConteo)->count(),
            'enviados' => (clone $baseConteo)->where('estado', 'ENVIADO')->count(),
            'revision' => (clone $baseConteo)->where('estado', 'EN_REVISION')->count(),
            'resueltos' => (clone $baseConteo)->where('estado', 'RESUELTO')->count(),
            'desestimados' => (clone $baseConteo)->where('estado', 'DESESTIMADO')->count(),
        ];

        return view('admin.reportes.index', compact('reportes', 'stats', 'q', 'estado'));
    }

    public function show($id)
    {
        $reporte = DB::table('reportes as r')
            ->leftJoin('usuarios as ur', 'ur.id_usuario', '=', 'r.reportante_usuario_id')
            ->leftJoin('motivos_reporte as mr', 'mr.id_motivo', '=', 'r.motivo_id')
            ->leftJoin('publicaciones_extravio as pe', function ($join) {
                $join->on('pe.id_publicacion', '=', 'r.objetivo_id')
                    ->where('r.objetivo_tipo', '=', 'PUB_EXTRAVIO');
            })
            ->leftJoin('usuarios as ud', 'ud.id_usuario', '=', 'pe.autor_usuario_id')
            ->leftJoin('usuarios as ua', 'ua.id_usuario', '=', 'r.revisado_por')
            ->where('r.objetivo_tipo', 'PUB_EXTRAVIO')
            ->where('r.id_reporte', $id)
            ->select(
                'r.*',
                'ur.nombre as reportante_nombre',
                'ur.correo as reportante_correo',
                'mr.nombre as motivo_nombre',
                'pe.id_publicacion',
                'pe.nombre as mascota_nombre',
                'pe.colonia_barrio',
                'pe.calle_referencias',
                'pe.descripcion as publicacion_descripcion',
                'pe.estado as estado_publicacion',
                'pe.fecha_extravio',
                'ud.nombre as dueno_nombre',
                'ud.correo as dueno_correo',
                'ua.nombre as admin_revisor_nombre'
            )
            ->first();

        abort_if(!$reporte, 404);

        $fotoPrincipal = DB::table('extravio_fotos')
            ->where('publicacion_id', $reporte->objetivo_id)
            ->orderBy('orden')
            ->first();

        return view('admin.reportes.show', compact('reporte', 'fotoPrincipal'));
    }

    public function marcarEnRevision($id)
    {
        $reporte = DB::table('reportes')
            ->where('id_reporte', $id)
            ->where('objetivo_tipo', 'PUB_EXTRAVIO')
            ->first();

        abort_if(!$reporte, 404);

        if ($reporte->estado !== 'ENVIADO') {
            return redirect()->route('admin.reportes.show', $id)
                ->with('error', 'Solo puedes marcar en revisión un reporte que está enviado.');
        }

        DB::table('reportes')
            ->where('id_reporte', $id)
            ->update([
                'estado' => 'EN_REVISION',
                'revisado_por' => Auth::user()->id_usuario,
            ]);

        return redirect()->route('admin.reportes.show', $id)
            ->with('success', 'El reporte fue marcado en revisión correctamente.');
    }

    public function resolver(Request $request, $id)
    {
        $reporte = DB::table('reportes')
            ->where('id_reporte', $id)
            ->where('objetivo_tipo', 'PUB_EXTRAVIO')
            ->first();

        abort_if(!$reporte, 404);

        if (in_array($reporte->estado, ['RESUELTO', 'DESESTIMADO'])) {
            return redirect()->route('admin.reportes.show', $id)
                ->with('error', 'Este reporte ya fue cerrado anteriormente.');
        }

        $data = $request->validate([
            'estado_final' => 'required|in:RESUELTO,DESESTIMADO',
            'nota_resolucion' => 'nullable|string|max:1000',
        ], [
            'estado_final.required' => 'Debes seleccionar una resolución.',
            'estado_final.in' => 'La resolución seleccionada no es válida.',
            'nota_resolucion.max' => 'La nota no debe exceder 1000 caracteres.',
        ]);

        DB::table('reportes')
            ->where('id_reporte', $id)
            ->update([
                'estado' => $data['estado_final'],
                'nota_resolucion' => $data['nota_resolucion'] ?: null,
                'revisado_por' => Auth::user()->id_usuario,
                'revisado_en' => now(),
            ]);

        $reporteActualizado = $this->obtenerReporteParaCorreo($id);

        if ($reporteActualizado) {
            $this->enviarCorreosResolucion($reporteActualizado);
        }

        return redirect()->route('admin.reportes.show', $id)
            ->with('success', 'El reporte fue actualizado correctamente.');
    }

    private function obtenerReporteParaCorreo($id)
    {
        return DB::table('reportes as r')
            ->leftJoin('usuarios as ur', 'ur.id_usuario', '=', 'r.reportante_usuario_id')
            ->leftJoin('motivos_reporte as mr', 'mr.id_motivo', '=', 'r.motivo_id')
            ->leftJoin('publicaciones_extravio as pe', function ($join) {
                $join->on('pe.id_publicacion', '=', 'r.objetivo_id')
                    ->where('r.objetivo_tipo', '=', 'PUB_EXTRAVIO');
            })
            ->leftJoin('usuarios as ud', 'ud.id_usuario', '=', 'pe.autor_usuario_id')
            ->leftJoin('usuarios as ua', 'ua.id_usuario', '=', 'r.revisado_por')
            ->where('r.objetivo_tipo', 'PUB_EXTRAVIO')
            ->where('r.id_reporte', $id)
            ->select(
                'r.id_reporte',
                'r.estado',
                'r.nota_resolucion',
                'r.creado_en',
                'r.revisado_en',
                'r.reportante_usuario_id',
                'r.objetivo_id',
                'ur.nombre as reportante_nombre',
                'ur.correo as reportante_correo',
                'ud.id_usuario as dueno_id',
                'ud.nombre as dueno_nombre',
                'ud.correo as dueno_correo',
                'mr.nombre as motivo_nombre',
                'pe.nombre as mascota_nombre',
                'pe.colonia_barrio',
                'pe.estado as estado_publicacion',
                'ua.nombre as admin_revisor_nombre'
            )
            ->first();
    }

    private function enviarCorreosResolucion($reporte): void
    {
        $this->enviarCorreoAlDueno($reporte);
        $this->enviarCorreoAlReportante($reporte);
    }

    private function enviarCorreoAlDueno($reporte): void
    {
        try {
            if (empty($reporte->dueno_correo)) {
                return;
            }

            $estadoBonito = $reporte->estado === 'RESUELTO' ? 'Resuelto' : 'Desestimado';

            $html = view('emails.reporte-resuelto-dueno', [
                'reporte' => $reporte,
                'estadoBonito' => $estadoBonito,
            ])->render();

            Mail::html($html, function ($message) use ($reporte, $estadoBonito) {
                $message->to($reporte->dueno_correo, $reporte->dueno_nombre)
                    ->subject("Actualización del reporte sobre tu publicación: {$estadoBonito}");
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoAlReportante($reporte): void
    {
        try {
            if (empty($reporte->reportante_correo)) {
                return;
            }

            if (!empty($reporte->dueno_correo) && $reporte->reportante_correo === $reporte->dueno_correo) {
                return;
            }

            $estadoBonito = $reporte->estado === 'RESUELTO' ? 'Resuelto' : 'Desestimado';

            $html = view('emails.reporte-resuelto-reportante', [
                'reporte' => $reporte,
                'estadoBonito' => $estadoBonito,
            ])->render();

            Mail::html($html, function ($message) use ($reporte, $estadoBonito) {
                $message->to($reporte->reportante_correo, $reporte->reportante_nombre)
                    ->subject("Resultado de tu reporte enviado: {$estadoBonito}");
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}