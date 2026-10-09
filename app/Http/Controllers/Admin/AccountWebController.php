<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;

class AccountWebController extends Controller
{
    public function index()
    {
        $accounts = UserAccount::with('createdByNode')->orderByDesc('created_at')->paginate(20);
        return view('admin.accounts.index', compact('accounts'));
    }

    public function show(string $id)
    {
        $account = UserAccount::with(['createdByNode', 'outgoingTransactions', 'incomingTransactions'])->findOrFail($id);
        return view('admin.accounts.show', compact('account'));
    }
}