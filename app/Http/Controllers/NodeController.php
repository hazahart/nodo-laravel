<?php

namespace App\Http\Controllers;

use App\Models\Nodo;
use App\Services\NodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NodeController extends Controller
{
    public function __construct(
        private NodeService $node,
    ) {}

    public function register(Request $request)
    {
        $request->validate([
            'url'    => 'required|url',
            'nombre' => 'nullable|string',
        ]);

        $nodo = $this->node->registrarNodo(
            $request->url,
            $request->nombre
        );

        Log::info('[Node] Nodo registrado', ['url' => $request->url]);

        return response()->json([
            'mensaje' => 'Nodo registrado correctamente',
            'nodo'    => $nodo,
        ], 201);
    }

    public function index()
    {
        $nodos = Nodo::where('activo', true)->get();

        return response()->json([
            'nodos' => $nodos,
            'total' => count($nodos),
        ]);
    }
}