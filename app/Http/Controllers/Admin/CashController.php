<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CashService;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function __construct(private CashService $cash) {}

    public function adjust(Request $request, string $nodeId)
    {
        $data = $request->validate([
            'type'  => 'required|in:allocation,replenishment,adjustment',
            'delta' => 'required|numeric|not_in:0',
        ]);

        try {
            $result = $this->cash->adjust($nodeId, $data['type'], $data['delta']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'       => 'Efectivo ajustado',
            'movement_id'   => $result->movement_id,
            'previous_cash' => $result->previous_cash,
            'new_cash'      => $result->new_cash,
        ]);
    }
}