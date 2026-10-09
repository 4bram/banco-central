<?php

namespace App\Services;

use App\Models\Node;
use App\Models\NodeCredential;
use App\Models\NodeResponsible;
use Illuminate\Support\Str;

class NodeService
{
    /**
     * Crea un nodo (branch o atm) + su responsable + su API Key.
     * Devuelve el nodo y la API Key en claro (solo se muestra una vez).
     */
    public function createNode(array $data): array
    {
        $node = Node::create([
            'name'           => $data['name'],
            'type'           => $data['type'],           // branch | atm
            'location'       => $data['location'] ?? null,
            'cash_available' => $data['cash_available'] ?? 0,
            'status'         => 'active',
        ]);

        // Responsable
        if (!empty($data['responsible'])) {
            NodeResponsible::create([
                'node_id'    => $node->id,
                'full_name'  => $data['responsible']['full_name'],
                'email'      => $data['responsible']['email'],
                'is_active'  => true,
            ]);
        }

        // API Key: generar en claro y guardar solo el hash
        $plainKey = 'sk_' . $node->type . '_' . Str::random(48);
        $keyHash  = hash('sha256', $plainKey);

        NodeCredential::create([
            'node_id'  => $node->id,
            'key_hash' => $keyHash,
            'status'   => 'active',
        ]);

        return [
            'node'    => $node,
            'api_key' => $plainKey,   // ⚠️ solo se muestra aquí
        ];
    }
}