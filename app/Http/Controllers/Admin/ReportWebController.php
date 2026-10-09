<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReportWebController extends Controller
{
    public function index()
    {
        $byNode = Transaction::select('node_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as monto'))
            ->with('node')
            ->groupBy('node_id')
            ->get();

        $byType = Transaction::select('type', DB::raw('count(*) as total'), DB::raw('sum(amount) as monto'))
            ->groupBy('type')
            ->get();

        $cashPerNode = Node::select('id', 'name', 'type', 'cash_available')
            ->orderByDesc('cash_available')
            ->get();

        return view('admin.reports.index', compact('byNode', 'byType', 'cashPerNode'));
    }
}
