<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyAuthApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Récupérer le token Bearer dans les headers de la requête
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token d\'authentification manquant.'
            ], 401);
        }

        try {
            // 2. Déterminer l'URL de validation depuis la configuration harmonisée
            $baseUrl = env('AUTH_API_BASE_URL');
            if (!$baseUrl) {
                $validateUrl = config('auth.validate_token_url');
                if ($validateUrl) {
                    $baseUrl = str_replace('/api/validate-token', '', $validateUrl);
                } else {
                    $baseUrl = 'http://authentication-api';
                }
            }
            $baseUrl = rtrim($baseUrl, '/');

            // 3. Interroger l'API d'authentification pour valider le token
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($baseUrl . '/api/validate-token');

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Token invalide ou expiré.',
                    'api_response_status' => $response->status(),
                    'api_response_body' => $response->json()
                ], 401);
            }

            // 4. Injecter les données de l'utilisateur dans la requête
            $userData = $response->json();
            // Harmonisation avec le format utilisé par l'API d'authentification
            // Supporte les formats: body.response_data, data, ou directement
            if (is_array($userData) && isset($userData['body']['response_data'])) {
                $userData = $userData['body']['response_data'];
            } elseif (is_array($userData) && isset($userData['data'])) {
                $userData = $userData['data'];
            }
            $request->merge(['auth_user' => $userData]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Service d\'authentification injoignable.',
                'debug_error' => $e->getMessage()
            ], 503);
        }

        return $next($request);
    }
}
