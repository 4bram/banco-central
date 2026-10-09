<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Models\Transaction;
use App\Models\UserAccount;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'branches'     => Node::where('type', 'branch')->count(),
            'atms'         => Node::where('type', 'atm')->count(),
            'accounts'     => UserAccount::count(),
            'transactions' => Transaction::count(),
            'cash_total'   => Node::sum('cash_available'),
        ];

        $recentTransactions = Transaction::with(['node', 'originAccount', 'destinationAccount'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTransactions'));
    }
}