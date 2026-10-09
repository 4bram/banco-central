<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['node', 'originAccount', 'destinationAccount'])->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('node_id')) {
            $query->where('node_id', $request->node_id);
        }

        $transactions = $query->paginate(25);
        $nodes = Node::orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'nodes'));
    }
}
