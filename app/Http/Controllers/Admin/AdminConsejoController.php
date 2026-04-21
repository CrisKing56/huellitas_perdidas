<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminConsejoController extends Controller
{
    private function usuarioAdminId()
    {
        return Auth::user()->id_usuario ?? null;
    }

    private function correoDestinoConsejo(Consejo $consejo): ?string
    {
        return $consejo->organizacion?->usuarioDueno?->correo
            ?? $consejo->organizacion?->usuarioDueno?->email
            ?? null;
    }

    private function enviarCorreoAprobado(Consejo $consejo): void
    {
        try {
            $correoDestino = $this->correoDestinoConsejo($consejo);

            if (!$correoDestino) {
                return;
            }

            $html = view('emails.consejo-aprobado', [
                'consejo' => $consejo,
                'urlMisConsejos' => route('consejos.mis-consejos'),
                'urlPublicacion' => route('consejos.show', $consejo->id_consejo),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $consejo) {
                $message->to($correoDestino)
                    ->subject('Tu consejo fue aprobado: ' . $consejo->titulo);
            });
        } catch (\Throwable $e) {
        }
    }

    private function enviarCorreoRechazado(Consejo $consejo): void
    {
        try {
            $correoDestino = $this->correoDestinoConsejo($consejo);

            if (!$correoDestino) {
                return;
            }

            $html = view('emails.consejo-rechazado', [
                'consejo' => $consejo,
                'urlMisConsejos' => route('consejos.mis-consejos'),
            ])->render();

            Mail::html($html, function ($message) use ($correoDestino, $consejo) {
                $message->to($correoDestino)
                    ->subject('Tu consejo fue rechazado: ' . $consejo->titulo);
            });
        } catch (\Throwable $e) {
        }
    }

    public function index(Request $request)
    {
        $filtros = [
            'estado' => (string) $request->get('estado', 'PENDIENTE'),
            'q' => trim((string) $request->get('q', '')),
        ];

        $query = Consejo::with([
            'organizacion.usuarioDueno',
            'categoria',
            'especie',
            'imagenes',
            'etiquetas',
        ]);

        if ($filtros['estado'] !== '') {
            $query->where('estado_publicacion', $filtros['estado']);
        }

        if ($filtros['q'] !== '') {
            $query->where(function ($sub) use ($filtros) {
                $sub->where('titulo', 'like', '%' . $filtros['q'] . '%')
                    ->orWhere('resumen', 'like', '%' . $filtros['q'] . '%')
                    ->orWhereHas('organizacion', function ($q) use ($filtros) {
                        $q->where('nombre', 'like', '%' . $filtros['q'] . '%');
                    });
            });
        }

        $consejos = $query
            ->orderByRaw("CASE 
                WHEN estado_publicacion = 'PENDIENTE' THEN 1
                WHEN estado_publicacion = 'RECHAZADO' THEN 2
                WHEN estado_publicacion = 'APROBADO' THEN 3
                ELSE 4
            END")
            ->orderBy('creado_en', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'pendientes' => Consejo::where('estado_publicacion', 'PENDIENTE')->count(),
            'aprobados' => Consejo::where('estado_publicacion', 'APROBADO')->count(),
            'rechazados' => Consejo::where('estado_publicacion', 'RECHAZADO')->count(),
            'total' => Consejo::count(),
        ];

        return view('admin.consejos.index', compact('consejos', 'stats', 'filtros'));
    }

    public function show($id)
    {
        $consejo = Consejo::with([
            'organizacion.usuarioDueno',
            'categoria',
            'especie',
            'imagenes',
            'etiquetas',
        ])->findOrFail($id);

        return view('admin.consejos.show', compact('consejo'));
    }

    public function aprobar($id)
    {
        $consejo = Consejo::with(['organizacion.usuarioDueno', 'categoria', 'especie'])->findOrFail($id);

        $consejo->estado_publicacion = 'APROBADO';
        $consejo->revisado_por = $this->usuarioAdminId();
        $consejo->revisado_en = now();
        $consejo->motivo_rechazo = null;
        $consejo->save();

        $this->enviarCorreoAprobado($consejo);

        return redirect()
            ->route('admin.consejos.index')
            ->with('success', 'El consejo fue aprobado correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'motivo_rechazo' => ['required', 'string', 'max:255'],
        ], [
            'motivo_rechazo.required' => 'Debes escribir el motivo de rechazo.',
            'motivo_rechazo.max' => 'El motivo de rechazo no debe exceder 255 caracteres.',
        ]);

        $consejo = Consejo::with(['organizacion.usuarioDueno', 'categoria', 'especie'])->findOrFail($id);

        $consejo->estado_publicacion = 'RECHAZADO';
        $consejo->revisado_por = $this->usuarioAdminId();
        $consejo->revisado_en = now();
        $consejo->motivo_rechazo = trim($request->motivo_rechazo);
        $consejo->save();

        $this->enviarCorreoRechazado($consejo);

        return redirect()
            ->route('admin.consejos.index')
            ->with('success', 'El consejo fue rechazado correctamente.');
    }
}