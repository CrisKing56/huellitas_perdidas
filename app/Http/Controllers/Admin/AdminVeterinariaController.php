<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Organizacion;


class AdminVeterinariaController extends Controller
{
    public function index(Request $request)
    {
        $activas = Organizacion::where('tipo', 'VETERINARIA')->where('estado_revision', 'APROBADA')->get();
        $pendientes = Organizacion::where('tipo', 'VETERINARIA')->where('estado_revision', 'PENDIENTE')->get();
        $rechazadas = Organizacion::where('tipo', 'VETERINARIA')->where('estado_revision', 'RECHAZADA')->get();

        $query = Organizacion::where('tipo', 'VETERINARIA');

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('telefono', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->filled('estado_revision')) {
            $query->where('estado_revision', $request->estado_revision);
        }

        $veterinarias = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.veterinarias.index', compact('veterinarias', 'activas', 'pendientes', 'rechazadas'));
    }

    public function show($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->leftJoin('direcciones as d', 'd.id_direccion', '=', 'o.direccion_id')
            ->leftJoin('ubicaciones as ub', 'ub.id_ubicacion', '=', 'o.ubicacion_id')
            ->leftJoin('veterinaria_detalle as vd', 'vd.organizacion_id', '=', 'o.id_organizacion')
            ->where('o.tipo', 'VETERINARIA')
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
                'vd.medico_responsable',
                'vd.cedula_profesional',
                'vd.num_veterinarios',
                'vd.otros_servicios'
            )
            ->first();

        abort_if(!$veterinaria, 404);

        $horarios = DB::table('horarios_atencion')
            ->where('organizacion_id', $id)
            ->orderBy('dia_semana')
            ->get();

        $servicios = DB::table('organizacion_servicio as os')
            ->join('servicios as s', 's.id_servicio', '=', 'os.servicio_id')
            ->where('os.organizacion_id', $id)
            ->pluck('s.nombre');

        $costos = DB::table('organizacion_costo_servicio as ocs')
            ->join('servicios as s', 's.id_servicio', '=', 'ocs.servicio_id')
            ->where('ocs.organizacion_id', $id)
            ->select('s.nombre', 'ocs.precio', 'ocs.moneda', 'ocs.nota')
            ->get();

        $fotos = DB::table('organizacion_fotos')
            ->where('organizacion_id', $id)
            ->orderBy('orden')
            ->get();

        return view('admin.veterinarias.show', compact(
            'veterinaria',
            'horarios',
            'servicios',
            'costos',
            'fotos'
        ));
    }

    public function aprobar($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'VETERINARIA')
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

        abort_if(!$veterinaria, 404);

        if ($veterinaria->estado_revision === 'APROBADA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('success', 'La veterinaria ya se encuentra aprobada.');
        }

        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'VETERINARIA')
            ->update([
                'estado_revision' => 'APROBADA',
                'motivo_rechazo'  => null,
                'updated_at'      => now(),
            ]);

        DB::table('usuarios')
            ->where('id_usuario', $veterinaria->id_usuario)
            ->update([
                'estado'     => 'ACTIVA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoAprobacion($veterinaria);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'Veterinaria aprobada correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'VETERINARIA')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.tipo',
                'o.estado_revision',
                'u.correo',
                'u.nombre as nombre_usuario'
            )
            ->first();

        abort_if(!$veterinaria, 404);

        if ($veterinaria->estado_revision === 'APROBADA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('error', 'Esta solicitud ya fue aprobada. Si después necesitas afectarla, lo correcto será suspenderla o reactivarla, no rechazarla.');
        }

        if ($veterinaria->estado_revision === 'RECHAZADA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('error', 'Esta solicitud ya se encuentra rechazada.');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|min:10|max:1000',
        ], [
            'motivo_rechazo.required' => 'Debes escribir el motivo del rechazo.',
            'motivo_rechazo.min' => 'El motivo debe tener al menos 10 caracteres.',
            'motivo_rechazo.max' => 'El motivo no debe exceder 1000 caracteres.',
        ]);

        DB::table('organizaciones')
            ->where('id_organizacion', $id)
            ->where('tipo', 'VETERINARIA')
            ->update([
                'estado_revision' => 'RECHAZADA',
                'motivo_rechazo'  => $request->motivo_rechazo,
                'updated_at'      => now(),
            ]);

        $this->enviarCorreoRechazo($veterinaria, $request->motivo_rechazo);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'Veterinaria rechazada correctamente.');
    }

    public function suspender($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'VETERINARIA')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.estado_revision',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario',
                'u.estado as estado_usuario'
            )
            ->first();

        abort_if(!$veterinaria, 404);

        if ($veterinaria->estado_revision !== 'APROBADA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('error', 'Solo puedes suspender una veterinaria que ya fue aprobada.');
        }

        if ($veterinaria->estado_usuario === 'SUSPENDIDA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('success', 'La cuenta de esta veterinaria ya estaba suspendida.');
        }

        DB::table('usuarios')
            ->where('id_usuario', $veterinaria->id_usuario)
            ->update([
                'estado'     => 'SUSPENDIDA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoSuspension($veterinaria);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'La cuenta de la veterinaria fue suspendida correctamente.');
    }

    public function reactivar($id)
    {
        $veterinaria = DB::table('organizaciones as o')
            ->join('usuarios as u', 'u.id_usuario', '=', 'o.usuario_dueno_id')
            ->where('o.id_organizacion', $id)
            ->where('o.tipo', 'VETERINARIA')
            ->select(
                'o.id_organizacion',
                'o.nombre',
                'o.estado_revision',
                'u.id_usuario',
                'u.correo',
                'u.nombre as nombre_usuario',
                'u.estado as estado_usuario'
            )
            ->first();

        abort_if(!$veterinaria, 404);

        if ($veterinaria->estado_revision !== 'APROBADA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('error', 'Solo puedes reactivar una veterinaria que ya fue aprobada.');
        }

        if ($veterinaria->estado_usuario === 'ACTIVA') {
            return redirect()->route('admin.veterinarias.show', $id)
                ->with('success', 'La cuenta de esta veterinaria ya estaba activa.');
        }

        DB::table('usuarios')
            ->where('id_usuario', $veterinaria->id_usuario)
            ->update([
                'estado'     => 'ACTIVA',
                'updated_at' => now(),
            ]);

        $this->enviarCorreoReactivacion($veterinaria);

        return redirect()->route('admin.veterinarias.show', $id)
            ->with('success', 'La cuenta de la veterinaria fue reactivada correctamente.');
    }

    private function enviarCorreoAprobacion(object $veterinaria): void
    {
        if (empty($veterinaria->correo)) {
            return;
        }

        $nombreCuenta = e($veterinaria->nombre_usuario ?: 'usuario');
        $nombreVet = e($veterinaria->nombre);
        $loginUrl = url('/login');

        $html = "
            <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                <h2 style='color: #16a34a;'>Solicitud aprobada</h2>
                <p>Hola <strong>{$nombreCuenta}</strong>,</p>
                <p>Tu solicitud de registro para la veterinaria <strong>{$nombreVet}</strong> fue aprobada correctamente.</p>
                <p>Ya puedes iniciar sesión en Huellitas Perdidas con el correo registrado.</p>
                <p><a href='{$loginUrl}' style='display:inline-block;padding:10px 18px;background:#f97316;color:white;text-decoration:none;border-radius:8px;'>Ir al inicio de sesión</a></p>
                <p style='margin-top: 24px;'>Saludos,<br>Equipo de Huellitas Perdidas</p>
            </div>
        ";

        try {
            Mail::html($html, function ($message) use ($veterinaria) {
                $message->to($veterinaria->correo, $veterinaria->nombre_usuario)
                    ->subject('Tu solicitud de veterinaria fue aprobada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoRechazo(object $veterinaria, string $motivo): void
    {
        if (empty($veterinaria->correo)) {
            return;
        }

        $nombreCuenta = e($veterinaria->nombre_usuario ?: 'usuario');
        $nombreVet = e($veterinaria->nombre);
        $motivoSeguro = nl2br(e($motivo));

        $html = "
            <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                <h2 style='color: #dc2626;'>Solicitud rechazada</h2>
                <p>Hola <strong>{$nombreCuenta}</strong>,</p>
                <p>Tu solicitud de registro para la veterinaria <strong>{$nombreVet}</strong> fue rechazada.</p>
                <p><strong>Motivo del rechazo:</strong></p>
                <div style='background:#fef2f2;border:1px solid #fecaca;padding:12px;border-radius:8px;color:#991b1b;'>
                    {$motivoSeguro}
                </div>
                <p style='margin-top: 16px;'>Puedes corregir la información y volver a registrarte.</p>
                <p style='margin-top: 24px;'>Saludos,<br>Equipo de Huellitas Perdidas</p>
            </div>
        ";

        try {
            Mail::html($html, function ($message) use ($veterinaria) {
                $message->to($veterinaria->correo, $veterinaria->nombre_usuario)
                    ->subject('Tu solicitud de veterinaria fue rechazada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoSuspension(object $veterinaria): void
    {
        if (empty($veterinaria->correo)) {
            return;
        }

        $nombreCuenta = e($veterinaria->nombre_usuario ?: 'usuario');
        $nombreVet = e($veterinaria->nombre);

        $html = "
            <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                <h2 style='color: #dc2626;'>Cuenta suspendida</h2>
                <p>Hola <strong>{$nombreCuenta}</strong>,</p>
                <p>La cuenta asociada a la veterinaria <strong>{$nombreVet}</strong> fue suspendida temporalmente por el administrador.</p>
                <p>Mientras permanezca suspendida, no podrás iniciar sesión en Huellitas Perdidas.</p>
                <p>Si consideras que esto es un error, por favor contacta al administrador.</p>
                <p style='margin-top: 24px;'>Saludos,<br>Equipo de Huellitas Perdidas</p>
            </div>
        ";

        try {
            Mail::html($html, function ($message) use ($veterinaria) {
                $message->to($veterinaria->correo, $veterinaria->nombre_usuario)
                    ->subject('Tu cuenta de veterinaria fue suspendida');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function enviarCorreoReactivacion(object $veterinaria): void
    {
        if (empty($veterinaria->correo)) {
            return;
        }

        $nombreCuenta = e($veterinaria->nombre_usuario ?: 'usuario');
        $nombreVet = e($veterinaria->nombre);
        $loginUrl = url('/login');

        $html = "
            <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                <h2 style='color: #2563eb;'>Cuenta reactivada</h2>
                <p>Hola <strong>{$nombreCuenta}</strong>,</p>
                <p>La cuenta asociada a la veterinaria <strong>{$nombreVet}</strong> fue reactivada correctamente.</p>
                <p>Ya puedes volver a iniciar sesión en Huellitas Perdidas.</p>
                <p><a href='{$loginUrl}' style='display:inline-block;padding:10px 18px;background:#f97316;color:white;text-decoration:none;border-radius:8px;'>Ir al inicio de sesión</a></p>
                <p style='margin-top: 24px;'>Saludos,<br>Equipo de Huellitas Perdidas</p>
            </div>
        ";

        try {
            Mail::html($html, function ($message) use ($veterinaria) {
                $message->to($veterinaria->correo, $veterinaria->nombre_usuario)
                    ->subject('Tu cuenta de veterinaria fue reactivada');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}