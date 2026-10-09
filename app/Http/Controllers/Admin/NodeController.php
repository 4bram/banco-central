<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Services\NodeService;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function __construct(private NodeService $nodes) {}

    public function index()
    {
        return response()->json([
            'nodes' => Node::with('responsibles')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                       => 'required|string|max:100',
            'type'                       => 'required|in:branch,atm',
            'location'                   => 'nullable|string',
            'cash_available'             => 'nullable|numeric|min:0',
            'responsible'                => 'nullable|array',
            'responsible.full_name'      => 'required_with:responsible|string|max:150',
            'responsible.email'          => 'required_with:responsible|email|max:150',
        ]);

        $result = $this->nodes->createNode($data);

        return response()->json([
            'message' => 'Nodo creado. Guarda la API Key, no se mostrará de nuevo.',
            'node'    => $result['node'],
            'api_key' => $result['api_key'],
        ], 201);
    }
}