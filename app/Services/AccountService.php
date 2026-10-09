<?php

namespace App\Services;

use App\Models\Node;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    /**
     * Abre una cuenta nueva. Solo lo puede hacer un nodo branch.
     */
    public function openAccount(Node $branch, array $data): UserAccount
    {
        if ($branch->type !== 'branch') {
            throw new \RuntimeException('Solo una sucursal puede abrir cuentas');
        }

        return UserAccount::create([
            'account_number'     => $data['account_number'],
            'holder_name'        => $data['holder_name'],
            'pin_hash'           => Hash::make($data['pin']),
            'balance'            => $data['initial_balance'] ?? 0,
            'status'             => 'active',
            'created_by_node_id' => $branch->id,
        ]);
    }

    public function findByAccountNumber(string $accountNumber): ?UserAccount
    {
        return UserAccount::where('account_number', $accountNumber)->first();
    }

    public function verifyPin(UserAccount $account, string $pin): bool
    {
        return Hash::check($pin, $account->pin_hash);
    }
}