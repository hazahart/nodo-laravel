<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use App\Services\NodeService;
use App\Services\EventLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct(
        private NodeService $node,
    ) {
    }

    public function store(Request $request)
    {
        $payload = $request->all();
        $datos = $payload['datos'] ?? $payload['transaccion'] ?? $payload['transaction'] ?? $payload;

        $existe = Transaccion::where('datos->persona_id', $datos['persona_id'] ?? null)
            ->where('datos->titulo_obtenido', $datos['titulo_obtenido'] ?? null)
            ->where('datos->fecha_fin', $datos['fecha_fin'] ?? null)
            ->where('minada', false)
            ->exists();

        if ($existe) {
            EventLogger::log('advertencia', 'Transacción duplicada ignorada', [
                'titulo' => $datos['titulo_obtenido'] ?? 'Desconocido',
            ]);
            return response()->json([
                'mensaje' => 'Transacción ya existe en este nodo',
            ], 200);
        }

        $transaccion = Transaccion::create([
            'datos' => $datos,
            'minada' => false,
        ]);

        EventLogger::log('transaccion', 'Transacción recibida', [
            'titulo' => $datos['titulo_obtenido'] ?? 'Desconocido',
        ]);

        Log::info('[Transaction] Nueva transacción recibida', [
            'id' => $transaccion->id,
            'datos' => $datos,
        ]);

        $this->node->propagarTransaccion($datos);

        return response()->json([
            'mensaje' => 'Transacción recibida y propagada',
            'transaccion' => $transaccion,
        ], 201);
    }

    public function index()
    {
        $pendientes = Transaccion::where('minada', false)->get();

        return response()->json([
            'pendientes' => $pendientes,
            'total' => count($pendientes),
        ]);
    }
}
