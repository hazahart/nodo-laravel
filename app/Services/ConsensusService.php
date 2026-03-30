<?php

namespace App\Services;

use App\Models\Grado;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Nodo;
use App\Services\EventLogger;

class ConsensusService
{
    public function __construct(
        private BlockchainService $blockchain
    ) {
    }

    public function resolver(): array
    {
        $nodos = Nodo::where('activo', true)->get();
        $cadenaActual = Grado::orderBy('creado_en')->get()->toArray();
        $longitudMaxima = count($cadenaActual);
        $nuevaCadena = null;
        $nodoGanador = null;

        foreach ($nodos as $nodo) {
            try {
                $response = Http::timeout(5)->get("{$nodo->url}/api/chain");

                if (!$response->ok())
                    continue;

                $data = $response->json();
                $cadenaRemota = $data['chain'] ?? $data['bloques'] ?? $data;

                if (is_array($cadenaRemota) && count($cadenaRemota) > $longitudMaxima) {
                    $longitudMaxima = count($cadenaRemota);
                    $nuevaCadena = $cadenaRemota;
                    $nodoGanador = $nodo->url;
                }
            } catch (\Exception $e) {
                Log::error('[Consensus] Error consultando nodo', [
                    'nodo' => $nodo->url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($nuevaCadena) {
            $this->reemplazarCadena($nuevaCadena);

            EventLogger::log('consenso', 'Cadena reemplazada por una más larga', [
                'fuente' => $nodoGanador,
                'longitud' => $longitudMaxima,
            ]);

            return [
                'reemplazada' => true,
                'mensaje' => 'Cadena reemplazada por la del nodo: ' . $nodoGanador,
                'longitud' => $longitudMaxima,
            ];
        }

        return [
            'reemplazada' => false,
            'mensaje' => 'Tu cadena ya es la más larga o está sincronizada',
            'longitud' => $longitudMaxima,
        ];
    }

    private function reemplazarCadena(array $nuevaCadena): void
    {
        Grado::truncate();

        foreach ($nuevaCadena as $bloque) {
            Grado::create([
                'persona_id' => $bloque['persona_id'] ?? null,
                'institucion_id' => $bloque['institucion_id'] ?? null,
                'programa_id' => $bloque['programa_id'] ?? null,
                'fecha_inicio' => $bloque['fecha_inicio'] ?? null,
                'fecha_fin' => $bloque['fecha_fin'] ?? null,
                'titulo_obtenido' => $bloque['titulo_obtenido'] ?? null,
                'numero_cedula' => $bloque['numero_cedula'] ?? null,
                'titulo_tesis' => $bloque['titulo_tesis'] ?? null,
                'menciones' => $bloque['menciones'] ?? null,
                'hash_actual' => $bloque['hash_actual'] ?? $bloque['hash'] ?? 'hash_' . uniqid(),
                'hash_anterior' => $bloque['hash_anterior'] ?? $bloque['previous_hash'] ?? '0',
                'nonce' => $bloque['nonce'] ?? 0,
                'firmado_por' => $bloque['firmado_por'] ?? 'NodoExterno',
            ]);
        }
    }
}
