<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Node;
use Illuminate\Http\Request;

class CashMovementWebController extends Controller
{
    public function index(Request $request)
    {
        $query = CashMovement::with('node')->orderByDesc('created_at');

        if ($request->filled('node_id')) {
            $query->where('node_id', $request->node_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(25);
        $nodes = Node::orderBy('name')->get();

        return view('admin.cash.index', compact('movements', 'nodes'));
    }
}