<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private AccountService $accounts) {}

    /**
     * POST /api/accounts
     * Solo nodos tipo "branch". Protegido por api.key:branch.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'account_number'  => 'required|string|min:8|max:20',
            'holder_name'     => 'required|string|max:150',
            'pin'             => 'required|string|min:4|max:8',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        $branch = $request->_api_node;

        try {
            $account = $this->accounts->openAccount($branch, $data);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Cuenta creada',
            'account' => $account,
        ], 201);
    }
}