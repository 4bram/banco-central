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
            $this->cash->adjust($nodeId, $data['type'], $data['delta']);
            return back()->with('success', 'Efectivo ajustado correctamente');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}