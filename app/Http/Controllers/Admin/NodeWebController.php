<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Services\NodeService;
use Illuminate\Http\Request;

class NodeWebController extends Controller
{
    public function __construct(private NodeService $nodes) {}

    public function index()
    {
        $nodes = Node::with('responsibles')->orderByDesc('created_at')->get();
        return view('admin.nodes.index', compact('nodes'));
    }

    public function create()
    {
        return view('admin.nodes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'type'                  => 'required|in:branch,atm',
            'location'              => 'nullable|string',
            'cash_available'        => 'nullable|numeric|min:0',
            'responsible.full_name' => 'nullable|string|max:150',
            'responsible.email'     => 'nullable|email|max:150',
        ]);

        $result = $this->nodes->createNode($data);

        return redirect()
            ->route('admin.nodes.show', $result['node']->id)
            ->with('success', 'Nodo creado correctamente')
            ->with('new_api_key', $result['api_key']);
    }

    public function show(string $id)
    {
        $node = Node::with(['responsibles', 'credentials', 'cashMovements', 'transactions'])->findOrFail($id);
        return view('admin.nodes.show', compact('node'));
    }
}