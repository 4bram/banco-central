<?php

namespace App\Http\Middleware;

use App\Models\NodeCredential;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next, ?string $nodeType = null): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key requerida'], 401);
        }

        $keyHash = hash('sha256', $apiKey);

        $credential = NodeCredential::with('node')
            ->where('key_hash', $keyHash)
            ->where('status', 'active')
            ->first();

        if (!$credential) {
            Log::warning('Intento con API Key inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'API Key inválida'], 401);
        }

        $node = $credential->node;

        if (!$node || $node->status !== 'active') {
            return response()->json(['error' => 'Nodo inactivo'], 403);
        }

        if ($nodeType && $node->type !== $nodeType) {
            return response()->json([
                'error' => "Operación no permitida para nodo tipo '{$node->type}'"
            ], 403);
        }

        $request->merge([
            '_api_node'       => $node,
            '_api_credential' => $credential,
        ]);

        return $next($request);
    }
}