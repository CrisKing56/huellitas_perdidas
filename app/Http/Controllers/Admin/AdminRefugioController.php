<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Organizacion;


class AdminRefugioController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas
        $activos = Organizacion::where('tipo', 'REFUGIO')->where('estado_revision', 'APROBADA')->get();
        $pendientes = Organizacion::where('tipo', 'REFUGIO')->where('estado_revision', 'PENDIENTE')->get();
        $rechazados = Organizacion::where('tipo', 'REFUGIO')->where('estado_revision', 'RECHAZADA')->get();

        // Consulta principal para la tabla
        $query = Organizacion::where('tipo', 'REFUGIO');

        if ($request->filled('q')) {
            $query->where('nombre', 'LIKE', '%' . $request->q . '%');
        }

        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        // Si usas paginación déjalo con paginate(10), si no, cámbialo a get()
        $refugios = $query->orderBy('created_at', 'desc')->paginate(10); 

        return view('admin.refugios.index', compact('refugios', 'activos', 'pendientes', 'rechazados'));
    }

    public function show($id)
    {
        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
            ->leftJoin('refugio_detalle as rd', 'rd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.tipo', 'REFUGIO')
            ->where('o.id_organizacion', $id)
            ->select(
                'o.*',
                'u.id_usuario',
                'u.nombre as nombre_usuario',
                'u.correo',
                'u.telefono as telefono_usuario',
                'u.whatsapp',
                'u.estado as estado_usuario',
                'd.calle_numero',
                'd.colonia',
                'd.codigo_postal',
                'd.ciudad',
                'd.estado as estado_direccion',
                'ub.latitud',
                'ub.longitud',
                'rd.tipo_organizacion',
                'rd.anio_fundacion',
                'rd.capacidad_total',
                'rd.animales_actuales',
                'rd.animales_dados_adopcion',
                'rd.anios_operacion',
                'rd.nombre_responsable',
                'rd.cargo_responsable',
                'rd.num_voluntarios',
                'rd.otras_especies'
            )
            ->first();

        abort_if(!$refugio, 404);

        $especies = DB::table('refugio_especie as re')
            ->join('especies as e', 'e.id_especie', '=', 're.especie_id')
            ->where('re.organizacion_id', $id)
            ->pluck('e.nombre');

        return view('admin.refugios.show', compact('refugio', 'especies'));
    }

    public function aprobar($id)
    {
        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'REFUGIO')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.tipo',
                'o.estado_revision',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario'
            )
            ->first();

        abort_if(!$refugio, 404);

        if ($refugio->estado_revision === 'APROBADA') {
            return redirect()->route('admin.refugios.show', $id)
                ->with('success', 'El refugio ya se encuentra aprobado.');
        }

        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'REFUGIO')
            ->update([
                'estado_revision' => 'APROBADA',
                'motivo_rechazo'  => null,
                'updated_at'      => now(),
            ]);

        DB::table('usuarios')
            ->where('id_usuario', $refugio->id_usuario)
            ->update([
                'estado'     => 'ACTIVA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoAprobacion($refugio);

        return redirect()->route('admin.refugios.show', $id)
            ->with('success', 'Refugio aprobado correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:1000',
        ], [
            'motivo_rechazo.required' => 'Debes escribir el motivo del rechazo.',
            'motivo_rechazo.max'      => 'El motivo del rechazo no debe exceder 1000 caracteres.',
        ]);

        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'REFUGIO')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.tipo',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario'
            )
            ->first();

        abort_if(!$refugio, 404);

        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'REFUGIO')
            ->update([
                'estado_revision' => 'RECHAZADA',
                'motivo_rechazo'  => trim($request->motivo_rechazo),
                'updated_at'      => now(),
            ]);

        DB::table('usuarios')
            ->where('id_usuario', $refugio->id_usuario)
            ->update([
                'estado'     => 'SUSPENDIDA',
                'updated_at' => now(),
            ]);

        $refugio->motivo_rechazo = trim($request->motivo_rechazo);
        $this->enviarCorreoRechazo($refugio);

        return redirect()->route('admin.refugios.show', $id)
            ->with('success', 'Refugio rechazado correctamente.');
    }

    public function suspender($id)
    {
        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'REFUGIO')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.tipo',
                'o.estado_revision',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario',
                'u.estado as estado_usuario'
            )
            ->first();

        abort_if(!$refugio, 404);

        DB::table('usuarios')
            ->where('id_usuario', $refugio->id_usuario)
            ->update([
                'estado'     => 'SUSPENDIDA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoSuspension($refugio);

        return redirect()->route('admin.refugios.show', $id)
            ->with('success', 'Cuenta del refugio suspendida correctamente.');
    }

    public function activar($id)
    {
        $refugio = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'REFUGIO')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.tipo',
                'o.estado_revision',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario',
                'u.estado as estado_usuario'
            )
            ->first();

        abort_if(!$refugio, 404);

        DB::table('usuarios')
            ->where('id_usuario', $refugio->id_usuario)
            ->update([
                'estado'     => 'ACTIVA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoActivacion($refugio);

        return redirect()->route('admin.refugios.show', $id)
            ->with('success', 'Cuenta del refugio activada correctamente.');
    }

    private function enviarCorreoAprobacion($refugio): void
    {
        try {
            if (empty($refugio->correo)) {
                return;
            }

            $html = view('emails.organizacion-aprobada', [
                'nombre' => $refugio->nombre_usuario,
                'organizacion' => $refugio->nombre,
                'tipo' => 'refugio',
            ])->render();

            Mail::html($html, function ($message) use ($refugio) {
                $message->to($refugio->correo, $refugio->nombre_usuario)
                    ->subject('Tu solicitud de refugio fue aprobada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoRechazo($refugio): void
    {
        try {
            if (empty($refugio->correo)) {
                return;
            }

            $html = view('emails.organizacion-rechazada', [
                'nombre' => $refugio->nombre_usuario,
                'organizacion' => $refugio->nombre,
                'tipo' => 'refugio',
                'motivo' => $refugio->motivo_rechazo ?? null,
            ])->render();

            Mail::html($html, function ($message) use ($refugio) {
                $message->to($refugio->correo, $refugio->nombre_usuario)
                    ->subject('Tu solicitud de refugio fue rechazada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoSuspension($refugio): void
    {
        try {
            if (empty($refugio->correo)) {
                return;
            }

            $html = view('emails.organizacion-suspendida', [
                'nombre' => $refugio->nombre_usuario,
                'organizacion' => $refugio->nombre,
                'tipo' => 'refugio',
            ])->render();

            Mail::html($html, function ($message) use ($refugio) {
                $message->to($refugio->correo, $refugio->nombre_usuario)
                    ->subject('Tu cuenta de refugio fue suspendida');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoActivacion($refugio): void
    {
        try {
            if (empty($refugio->correo)) {
                return;
            }

            $html = view('emails.organizacion-reactivada', [
                'nombre' => $refugio->nombre_usuario,
                'organizacion' => $refugio->nombre,
                'tipo' => 'refugio',
            ])->render();

            Mail::html($html, function ($message) use ($refugio) {
                $message->to($refugio->correo, $refugio->nombre_usuario)
                    ->subject('Tu cuenta de refugio fue reactivada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}