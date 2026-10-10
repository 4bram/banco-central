<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactions,
        private AccountService $accounts,
    ) {}

    /**
     * POST /api/withdraw
     * Solo nodos tipo "atm". Requiere PIN.
     */
    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'account_number' => 'required|string',
            'pin'            => 'required|string',
            'amount'         => 'required|numeric|min:0.01',
        ]);

        $account = $this->accounts->findByAccountNumber($data['account_number']);
        if (!$account) {
            return response()->json(['error' => 'Cuenta no encontrada'], 404);
        }

        if (!Hash::check($data['pin'], $account->pin_hash)) {
            return response()->json(['error' => 'PIN incorrecto'], 401);
        }

        $node = $request->_api_node;

        // Leer el header de idempotencia
        $idempotencyKey = $request->header('Idempotency-Key');

        try {
            $result = $this->transactions->process(
                nodeId: $node->id,
                type: 'withdrawal',
                originAccountId: $account->id,
                destinationAccountId: null,
                amount: $data['amount'],
                idempotencyKey: $idempotencyKey,
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'        => 'Retiro exitoso',
            'transaction_id' => $result->transaction_id,
            'balance_after'  => $result->balance_after,
            'cash_after'     => $result->cash_after,
            'replayed'       => $result->replayed,
        ]);
    }

    /**
     * POST /api/deposit
     * Solo nodos tipo "atm".
     */
    public function deposit(Request $request)
    {
        $data = $request->validate([
            'account_number' => 'required|string',
            'amount'         => 'required|numeric|min:0.01',
        ]);

        $account = $this->accounts->findByAccountNumber($data['account_number']);
        if (!$account) {
            return response()->json(['error' => 'Cuenta no encontrada'], 404);
        }

        $node = $request->_api_node;

        // Leer el header de idempotencia
        $idempotencyKey = $request->header('Idempotency-Key');

        try {
            $result = $this->transactions->process(
                nodeId: $node->id,
                type: 'deposit',
                originAccountId: null,
                destinationAccountId: $account->id,
                amount: $data['amount'],
                idempotencyKey: $idempotencyKey,
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'        => 'Depósito exitoso',
            'transaction_id' => $result->transaction_id,
            'balance_after'  => $result->balance_after,
            'cash_after'     => $result->cash_after,
            'replayed'       => $result->replayed,
        ]);
    }

    /**
     * POST /api/transfer
     * Solo nodos tipo "branch".
     */
    public function transfer(Request $request)
    {
        $data = $request->validate([
            'from_account_number' => 'required|string',
            'to_account_number'   => 'required|string',
            'amount'              => 'required|numeric|min:0.01',
        ]);

        $origin      = $this->accounts->findByAccountNumber($data['from_account_number']);
        $destination = $this->accounts->findByAccountNumber($data['to_account_number']);

        if (!$origin || !$destination) {
            return response()->json(['error' => 'Cuenta origen o destino no encontrada'], 404);
        }

        $node = $request->_api_node;

        // Leer el header de idempotencia
        $idempotencyKey = $request->header('Idempotency-Key');

        try {
            $result = $this->transactions->process(
                nodeId: $node->id,
                type: 'transfer',
                originAccountId: $origin->id,
                destinationAccountId: $destination->id,
                amount: $data['amount'],
                idempotencyKey: $idempotencyKey,
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'        => 'Transferencia exitosa',
            'transaction_id' => $result->transaction_id,
            'replayed'       => $result->replayed,
        ]);
    }
}