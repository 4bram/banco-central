<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function __construct(private SupabaseAuthService $auth) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->auth->login($data['email'], $data['password']);

        if (!$result) {
            return back()->withErrors(['email' => 'Credenciales inválidas']);
        }

        $profile = $this->auth->getAdminProfile($result['user']['id']);

        if (!$profile || !$profile->is_active) {
            return back()->withErrors(['email' => 'Usuario no autorizado como admin']);
        }

        Session::put('admin', [
            'id'           => $profile->id,
            'email'        => $profile->email ?? $data['email'],
            'full_name'    => $profile->full_name,
            'role'         => $profile->role,
            'access_token' => $result['access_token'],
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Session::forget('admin');
        return redirect()->route('admin.login');
    }
}