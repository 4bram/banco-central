<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Procesa una operación bancaria usando la función RPC de Postgres.
     * Esto garantiza atomicidad e idempotencia.
     */
    public function process(
        string $nodeId,
        string $type,                     // deposit | withdrawal | transfer
        ?string $originAccountId,
        ?string $destinationAccountId,
        float $amount,
        ?string $idempotencyKey = null
    ): object {
        $idempotencyKey = $idempotencyKey ?: (string) Str::uuid();

        $result = DB::selectOne(
            'SELECT * FROM public.process_bank_transaction(?, ?, ?, ?, ?, ?)',
            [
                $nodeId,
                $type,
                $originAccountId,
                $destinationAccountId,
                $amount,
                $idempotencyKey,
            ]
        );

        return $result;
    }
}