<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthApiService
{
    /**
     * URL de base de l'API d'authentification
     */
    private string $baseUrl;

    /**
     * Token de l'admin mis en cache pour les appels exécutés hors session utilisateur (ex: console / seeders)
     */
    private static ?string $serviceToken = null;

    public function __construct()
    {
        // Détermine l'URL de base à partir des variables d'environnement
        $baseUrl = env('AUTH_API_BASE_URL');
        if (!$baseUrl) {
            $validateUrl = env('AUTH_VALIDATE_TOKEN_URL');
            if ($validateUrl) {
                $baseUrl = str_replace('/api/validate-token', '', $validateUrl);
            } else {
                $baseUrl = 'http://authentication-api';
            }
        }
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Récupère les données utiles (body.response_data) venant de toutes les réponses de authentication-api.
     */
    public function extractResponseData(Response $response): mixed
    {
        return $response->json('body.response_data');
    }

    /**
     * Récupère le token à utiliser pour les actions sur authentication-api :
     * - Si un utilisateur est connecté actuellement depuis employe-api, utilise son Bearer token.
     * - Sinon (ex: exécution depuis un terminal avec php artisan db:seed), authentifie l'admin
     *   (le premier utilisateur créé dans authentication-api) et utilise son token.
     */
    private function authenticate(): string
    {
        // 1. Utiliser le token de l'utilisateur connecté actuellement depuis employe-api s'il existe
        if (!app()->runningInConsole()) {
            $connectedUserToken = request()?->bearerToken();
            if (!empty($connectedUserToken)) {
                return $connectedUserToken;
            }
        } elseif (!empty(request()?->bearerToken())) {
            return request()->bearerToken();
        }

        // 2. Sinon (console / seeder), utiliser le token de l'admin (premier user de authentication-api)
        if (self::$serviceToken) {
            return self::$serviceToken;
        }

        $username = env('AUTH_API_SERVICE_USERNAME', 'admin');
        $password = env('AUTH_API_SERVICE_PASSWORD', '123456');

        if (!$username || !$password) {
            throw new \RuntimeException('Les variables AUTH_API_SERVICE_USERNAME et AUTH_API_SERVICE_PASSWORD doivent être définies dans le fichier .env');
        }

        $response = Http::acceptJson()->post("{$this->baseUrl}/api/login", [
            'username' => $username,
            'password' => $password,
        ]);

        Log::debug('AuthApiService::authenticate() response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $response->throw();

        // AuthController::login retourne via respondLogin() :
        // body.response_data = ['user' => [...], 'token' => $token, 'expires_at' => $expiresAt]
        $data = $this->extractResponseData($response);

        Log::debug('AuthApiService::authenticate() extracted response data', [
            'data' => $data,
        ]);
        
        self::$serviceToken = is_array($data) && !empty($data['token']) && is_string($data['token'])
            ? $data['token']
            : null;

        if (!self::$serviceToken) {
            throw new \RuntimeException('Impossible de récupérer le token du service d\'authentification');
        }

        return self::$serviceToken;
    }

    /**
     * Crée un utilisateur dans l'API d'authentification
     */
    public function createUser(string $username, string $email, string $firstName, string $lastName): string
    {
        $token = $this->authenticate();

        $password = env('AUTH_API_DEFAULT_PASSWORD', '12345678');

        $response = Http::acceptJson()->withToken($token)->post("{$this->baseUrl}/api/user", [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        // Si l'utilisateur existe déjà (respondBadRequest renvoie 422), on le récupère
        if ($response->status() === 409 || $response->status() === 422) {
            return $this->findUserIdByEmail($token, $email);
        }

        $response->throw();

        // UserController::store retourne respondOk($user) -> body.response_data = ['id' => ..., ...]
        $user = $this->extractResponseData($response);

        if (is_array($user) && isset($user['id'])) {
            return (string) $user['id'];
        }

        throw new \RuntimeException('Réponse inattendue de l\'API d\'authentification lors de la création de l\'utilisateur');
    }

    /**
     * Supprime un utilisateur dans l'API d'authentification
     */
    public function deleteUser(string $userId): void
    {
        $token = $this->authenticate();

        $response = Http::acceptJson()->withToken($token)->delete("{$this->baseUrl}/api/user/{$userId}");

        // Si l'utilisateur n'existe pas (respondNotFound renvoie 404), on considère l'opération comme réussie
        if ($response->status() === 404) {
            return;
        }

        $response->throw();
    }

    /**
     * Active ou désactive un utilisateur dans l'API d'authentification
     */
    public function setUserEnabled(string $userId, bool $enabled): void
    {
        $token = $this->authenticate();

        $response = Http::acceptJson()->withToken($token)->put("{$this->baseUrl}/api/user/{$userId}", [
            'enabled' => $enabled,
            'status' => $enabled,
        ]);

        // Si l'utilisateur n'existe pas, on ignore l'opération
        if ($response->status() === 404) {
            return;
        }

        // Si l'API ne prend pas en charge ce champ, on ignore sans erreur
        if ($response->failed() && $response->status() !== 422) {
            $response->throw();
        }
    }

    /**
     * Recherche un utilisateur par son adresse email
     */
    private function findUserIdByEmail(string $token, string $email): string
    {
        $response = Http::acceptJson()->withToken($token)->get("{$this->baseUrl}/api/user", [
            'query_' => [
                'where' => [
                    'email' => $email,
                ],
            ],
        ]);

        $response->throw();

        // UserController::index retourne respondOk($query->get()) -> body.response_data = [['id' => ..., ...]]
        $users = $this->extractResponseData($response);

        if (is_array($users) && !empty($users)) {
            if (isset($users[0]['id'])) {
                return (string) $users[0]['id'];
            }
        }

        throw new \RuntimeException("Aucun utilisateur trouvé avec l'email {$email}");
    }
}
