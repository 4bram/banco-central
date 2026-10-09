<?php

namespace App\Services;

use App\Models\AdminProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseAuthService
{
    protected string $url;
    protected string $publishableKey;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL'), '/');
        $this->publishableKey = env('SUPABASE_PUBLISHABLE_KEY');
    }

    /**
     * Autentica email+password contra Supabase Auth.
     * Devuelve [user, access_token] o null.
     */
    public function login(string $email, string $password): ?array
    {
        $response = Http::withHeaders([
            'apikey'       => $this->publishableKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->url}/auth/v1/token?grant_type=password", [
            'email'    => $email,
            'password' => $password,
        ]);

        if (!$response->ok()) {
            Log::warning('Supabase Auth falló', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        return [
            'user'         => $data['user'] ?? null,
            'access_token' => $data['access_token'] ?? null,
        ];
    }

    /**
     * Obtiene el profile local del admin desde admin_profiles.
     */
    public function getAdminProfile(string $authUserId): ?AdminProfile
    {
        return AdminProfile::where('id', $authUserId)->first();
    }
}