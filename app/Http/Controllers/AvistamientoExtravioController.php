<?php

namespace App\Http\Controllers;

use App\Models\AvistamientoExtravio;
use App\Models\PublicacionExtravio;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AvistamientoExtravioController extends Controller
{
    public function store(Request $request, $id)
    {
        $publicacion = PublicacionExtravio::with('autor')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        $request->validate([
            'nombre_contacto' => 'nullable|string|max:120',
            'telefono_contacto' => 'nullable|string|max:20',
            'fecha_avistamiento' => 'nullable|date',
            'colonia_barrio' => 'nullable|string|max:120',
            'calle_referencias' => 'nullable|string|max:200',
            'descripcion' => 'required|string|max:1500',
            'foto' => 'nullable|image|max:5120',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ], [
            'descripcion.required' => 'Debes escribir una descripción del avistamiento.',
            'descripcion.max' => 'La descripción no debe exceder 1500 caracteres.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La fotografía no debe pesar más de 5 MB.',
        ]);

        if (($request->filled('latitud') && !$request->filled('longitud')) || (!$request->filled('latitud') && $request->filled('longitud'))) {
            return back()
                ->withInput()
                ->withErrors(['descripcion' => 'Si marcas ubicación, debes enviar latitud y longitud completas.']);
        }

        try {
            $avistamiento = null;

            DB::transaction(function () use ($request, $publicacion, &$avistamiento) {
                $ubicacionId = null;

                if ($request->filled('latitud') && $request->filled('longitud')) {
                    $ubicacion = Ubicacion::create([
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'precision_metros' => null,
                    ]);

                    $ubicacionId = $ubicacion->id_ubicacion;
                }

                $fotoUrl = null;
                if ($request->hasFile('foto')) {
                    $fotoUrl = $request->file('foto')->store('avistamientos', 'public');
                }

                $avistamiento = AvistamientoExtravio::create([
                    'publicacion_id' => $publicacion->id_publicacion,
                    'usuario_reportante_id' => Auth::user()->id_usuario,
                    'ubicacion_id' => $ubicacionId,
                    'nombre_contacto' => $request->nombre_contacto ?: (Auth::user()->nombre ?? null),
                    'telefono_contacto' => $request->telefono_contacto ?: (Auth::user()->telefono ?? null),
                    'fecha_avistamiento' => $request->fecha_avistamiento ?: null,
                    'colonia_barrio' => $request->colonia_barrio ?: null,
                    'calle_referencias' => $request->calle_referencias ?: null,
                    'descripcion' => trim($request->descripcion),
                    'foto_url' => $fotoUrl,
                    'estado' => 'ENVIADO',
                    'creado_en' => now(),
                ]);
            });

            if ($avistamiento) {
                $this->enviarCorreoAvistamiento($publicacion, $avistamiento);
            }

            return redirect(route('extravios.show', $publicacion->id_publicacion) . '#avistamientos')
                ->with('success', 'Avistamiento enviado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => 'No se pudo guardar el avistamiento: ' . $e->getMessage()
            ]);
        }
    }

    public function marcarVisto($id)
    {
        $avistamiento = AvistamientoExtravio::with('publicacion')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        if ((int) Auth::user()->id_usuario !== (int) $avistamiento->publicacion->autor_usuario_id) {
            abort(403);
        }

        if ($avistamiento->estado !== 'VISTO') {
            $avistamiento->update([
                'estado' => 'VISTO',
                'visto_en' => now(),
            ]);
        }

        return back()->with('success', 'El avistamiento fue marcado como visto.');
    }

    public function descartar($id)
    {
        $avistamiento = AvistamientoExtravio::with('publicacion')->findOrFail($id);

        if (!Auth::check() || !isset(Auth::user()->id_usuario)) {
            return redirect()->route('login');
        }

        if ((int) Auth::user()->id_usuario !== (int) $avistamiento->publicacion->autor_usuario_id) {
            abort(403);
        }

        $avistamiento->update([
            'estado' => 'DESCARTADO',
        ]);

        return back()->with('success', 'El avistamiento fue descartado.');
    }

    private function enviarCorreoAvistamiento(PublicacionExtravio $publicacion, AvistamientoExtravio $avistamiento): void
    {
        try {
            $dueno = DB::table('usuarios')
                ->where('id_usuario', $publicacion->autor_usuario_id)
                ->select('nombre', 'correo')
                ->first();

            if (!$dueno || empty($dueno->correo)) {
                return;
            }

            $urlPublicacion = route('extravios.show', $publicacion->id_publicacion) . '#avistamientos';

            $html = view('emails.avistamiento-recibido', [
                'duenoNombre' => $dueno->nombre,
                'publicacion' => $publicacion,
                'avistamiento' => $avistamiento,
                'urlPublicacion' => $urlPublicacion,
            ])->render();

            Mail::html($html, function ($message) use ($dueno, $publicacion) {
                $message->to($dueno->correo, $dueno->nombre)
                    ->subject('Nuevo avistamiento reportado de ' . $publicacion->nombre);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}