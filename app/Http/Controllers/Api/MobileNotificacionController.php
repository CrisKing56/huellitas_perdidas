<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class MobileNotificacionController extends Controller
{
    private function mapItem(Notificacion $item): array
    {
        return [
            'id_notificacion' => (int) $item->id_notificacion,
            'usuario_id' => (int) $item->usuario_id,
            'tipo' => $item->tipo,
            'titulo' => $item->titulo,
            'mensaje' => $item->mensaje,
            'leido' => (bool) $item->leido,
            'creado_en' => $item->creado_en
                ? $item->creado_en->toDateTimeString()
                : null,
            'tiempo' => $item->creado_en
                ? $item->creado_en->locale('es')->diffForHumans()
                : null,
        ];
    }

    public function index($idUsuario)
    {
        $items = Notificacion::query()
            ->where('usuario_id', (int) $idUsuario)
            ->orderByDesc('id_notificacion')
            ->get()
            ->map(function (Notificacion $item) {
                return $this->mapItem($item);
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $items,
        ]);
    }

    public function count($idUsuario)
    {
        $count = Notificacion::query()
            ->where('usuario_id', (int) $idUsuario)
            ->where('leido', 0)
            ->count();

        return response()->json([
            'ok' => true,
            'count' => $count,
        ]);
    }

    public function leer(Request $request, $id)
    {
        /** @var Notificacion|null $item */
        $item = Notificacion::query()->find($id);

        if (!$item) {
            return response()->json([
                'ok' => false,
                'message' => 'Notificación no encontrada',
            ], 404);
        }

        $idUsuario = (int) ($request->input('id_usuario') ?? 0);

        if ($idUsuario > 0 && (int) $item->usuario_id !== $idUsuario) {
            return response()->json([
                'ok' => false,
                'message' => 'No tienes permiso para modificar esta notificación',
            ], 403);
        }

        $item->leido = 1;
        $item->save();

        return response()->json([
            'ok' => true,
            'message' => 'Notificación marcada como leída',
            'data' => $this->mapItem($item),
        ]);
    }

    public function leerTodas(Request $request, $idUsuario)
    {
        Notificacion::query()
            ->where('usuario_id', (int) $idUsuario)
            ->where('leido', 0)
            ->update([
                'leido' => 1,
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Todas las notificaciones fueron marcadas como leídas',
        ]);
    }
}