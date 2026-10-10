<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * GET /api/nodes/me/transactions
     * Historial de transacciones del nodo autenticado.
     */
    public function nodeTransactions(Request $request)
    {
        $node = $request->_api_node;

        $query = Transaction::with(['originAccount:id,account_number,holder_name', 'destinationAccount:id,account_number,holder_name'])
            ->where('node_id', $node->id)
            ->orderByDesc('created_at');

        $this->applyFilters($query, $request);

        $transactions = $query->limit($request->input('limit', 100))->get();

        return response()->json([
            'node' => [
                'id'   => $node->id,
                'name' => $node->name,
                'type' => $node->type,
            ],
            'count'        => $transactions->count(),
            'transactions' => $transactions->map(fn($t) => $this->formatTransaction($t)),
        ]);
    }

    /**
     * GET /api/accounts/{accountNumber}/transactions
     * Historial por cuenta específica.
     */
    public function accountTransactions(Request $request, string $accountNumber)
    {
        $account = UserAccount::where('account_number', $accountNumber)->first();
        if (!$account) {
            return response()->json(['error' => 'Cuenta no encontrada'], 404);
        }

        $query = Transaction::with(['node:id,name,type', 'originAccount:id,account_number', 'destinationAccount:id,account_number'])
            ->where(function ($q) use ($account) {
                $q->where('account_origin_id', $account->id)
                  ->orWhere('account_destination_id', $account->id);
            })
            ->orderByDesc('created_at');

        $this->applyFilters($query, $request);

        $transactions = $query->limit($request->input('limit', 100))->get();

        return response()->json([
            'account' => [
                'account_number' => $account->account_number,
                'holder_name'    => $account->holder_name,
                'balance'        => $account->balance,
                'status'         => $account->status,
            ],
            'count'        => $transactions->count(),
            'transactions' => $transactions->map(fn($t) => $this->formatTransaction($t)),
        ]);
    }

    /**
     * GET /api/accounts/search?q=...
     * Buscar cuentas por número o titular.
     */
    public function searchAccounts(Request $request)
    {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) {
            return response()->json(['error' => 'Se requiere al menos 2 caracteres en "q"'], 422);
        }

        $accounts = UserAccount::where('account_number', 'ilike', "%{$q}%")
            ->orWhere('holder_name', 'ilike', "%{$q}%")
            ->orderBy('account_number')
            ->limit(50)
            ->get(['id', 'account_number', 'holder_name', 'balance', 'status']);

        return response()->json([
            'count'    => $accounts->count(),
            'accounts' => $accounts,
        ]);
    }

    /**
     * GET /api/nodes/me/reports
     * Reporte administrativo del nodo.
     */
    public function nodeReport(Request $request)
    {
        $node = $request->_api_node;

        $query = Transaction::where('node_id', $node->id);
        $this->applyFilters($query, $request);

        $byType = (clone $query)
            ->select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as amount'))
            ->groupBy('type')
            ->get();

        $totals = (clone $query)
            ->select(DB::raw('count(*) as total_count'), DB::raw('sum(amount) as total_amount'))
            ->first();

        return response()->json([
            'node' => [
                'id'   => $node->id,
                'name' => $node->name,
                'type' => $node->type,
            ],
            'period' => [
                'from' => $request->input('from'),
                'to'   => $request->input('to'),
            ],
            'summary' => [
                'total_transactions' => (int) $totals->total_count,
                'total_amount'       => number_format((float) $totals->total_amount, 2, '.', ''),
                'by_type' => $byType->map(fn($r) => [
                    'type'   => $r->type,
                    'count'  => (int) $r->count,
                    'amount' => number_format((float) $r->amount, 2, '.', ''),
                ]),
            ],
        ]);
    }

    /**
     * Aplica filtros comunes (type, from, to).
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to'));
        }
    }

    /**
     * Formato uniforme de transacción.
     */
    private function formatTransaction(Transaction $t): array
    {
        return [
            'id'                   => $t->id,
            'type'                 => $t->type,
            'amount'               => $t->amount,
            'node'                 => $t->relationLoaded('node') && $t->node ? [
                'id'   => $t->node->id,
                'name' => $t->node->name,
                'type' => $t->node->type,
            ] : null,
            'origin_account'       => $t->originAccount?->account_number,
            'destination_account'  => $t->destinationAccount?->account_number,
            'created_at'           => $t->created_at->toIso8601String(),
        ];
    }
}