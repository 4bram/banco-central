<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CashService
{
    /**
     * Ajusta el efectivo físico de un nodo (carga, reabastecimiento, ajuste).
     */
    public function adjust(
        string $nodeId,
        string $type,          // allocation | replenishment | adjustment
        float $delta           // con signo: positivo suma, negativo resta
    ): object {
        return DB::selectOne(
            'SELECT * FROM public.adjust_node_cash(?, ?, ?)',
            [$nodeId, $type, $delta]
        );
    }
}