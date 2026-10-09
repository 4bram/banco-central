<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __construct(private AccountService $accounts) {}

    /**
     * GET /api/balance/{accountNumber}
     * Cualquier nodo autenticado (branch o atm).
     */
    public function show(Request $request, string $accountNumber)
    {
        $account = $this->accounts->findByAccountNumber($accountNumber);

        if (!$account) {
            return response()->json(['error' => 'Cuenta no encontrada'], 404);
        }

        return response()->json([
            'account_number' => $account->account_number,
            'holder_name'    => $account->holder_name,
            'balance'        => $account->balance,
            'status'         => $account->status,
        ]);
    }
}