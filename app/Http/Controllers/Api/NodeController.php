<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    /**
     * GET /api/nodes/me
     * Devuelve los datos del nodo autenticado por su API Key.
     */
    public function me(Request $request)
    {
        $node = $request->_api_node;

        return response()->json([
            'id'       => $node->id,
            'name'     => $node->name,
            'location' => $node->location ?? 'Sin ubicación',
            'type'     => $node->type,
            'cash'     => (float) $node->cash_available,
            'status'   => $node->status,
        ]);
    }
}
