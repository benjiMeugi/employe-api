<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KeycloakAdminService
{
    private string $baseUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;
    private string $defaultPassword;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('KEYCLOAK_BASE_URL'), '/');
        $this->realm = env('KEYCLOAK_REALM');
        $this->clientId = env('KEYCLOAK_CLIENT_ID');
        $this->clientSecret = env('KEYCLOAK_CLIENT_SECRET');
        $this->defaultPassword = env('KEYCLOAK_DEFAULT_PASSWORD');
    }

    /**
     * Récupère un token au nom du client lui-même (employe-api), pas d'un
     * utilisateur humain — nécessite que "Service accounts roles" soit
     * activé sur le client, avec le rôle manage-users du client
     * realm-management assigné (voir Service accounts roles côté Keycloak).
     */
    private function getServiceAccountToken(): string
    {
        $response = Http::asForm()->post(
            "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token",
            [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]
        );

        $response->throw();

        return $response->json('access_token');
    }

    /**
     * Crée un utilisateur Keycloak, lui fixe le mot de passe de première
     * connexion commun, et renvoie son id (le "sub" qui apparaîtra
     * ensuite dans les tokens émis pour cette personne).
     *
     * Gère deux pièges connus :
     * - Keycloak renvoie un 201 sans aucun corps — l'id n'est que dans
     *   l'en-tête Location, jamais dans une réponse JSON à lire.
     * - Un appel rejoué après un timeout réseau peut recevoir un 409
     *   (compte déjà créé la première fois) — traité comme un succès,
     *   pas une erreur, pour rester idempotent. Dans ce cas, le mot de
     *   passe n'est PAS retouché : la personne a peut-être déjà changé
     *   le sien, le réinitialiser serait une régression, pas un service.
     */
    public function createUser(string $username, string $email, string $firstName, string $lastName): string
    {
        $token = $this->getServiceAccountToken();

        $response = Http::withToken($token)->post(
            "{$this->baseUrl}/admin/realms/{$this->realm}/users",
            [
                'username' => $username,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'enabled' => true,
                'emailVerified' => true,
            ]
        );

        if ($response->status() === 201) {
            $location = $response->header('Location');
            $userId = basename(parse_url($location, PHP_URL_PATH));

            // Mot de passe par défaut : UNIQUEMENT en développement local.
            // En production, rien n'est jamais défini ici — c'est
            // volontaire, pas un oubli. Un admin gère les mots de passe
            // directement dans Keycloak, jamais via ce code.
            if (app()->environment('local')) {
                $this->setDefaultPassword($token, $userId);
            }

            return $userId;
        }

        if ($response->status() === 409) {
            return $this->findUserIdByEmail($token, $email);
        }

        // $response->throw() ne se déclenche que sur un vrai statut
        // d'échec (4xx/5xx) — un statut inattendu mais "réussi" au sens
        // HTTP (ex: 200 au lieu de 201) ne le ferait pas paniquer,
        // laissant sinon cette fonction se terminer sans renvoyer de
        // string, malgré sa signature. Cette ligne couvre ce trou.
        $response->throw();

        throw new \RuntimeException(
            "Réponse Keycloak inattendue (status {$response->status()}) lors de la création de l'utilisateur."
        );
    }

    /**
     * Supprime définitivement un compte Keycloak — appelé quand
     * l'employé correspondant est supprimé (pas juste désactivé).
     */
    public function deleteUser(string $userId): void
    {
        $token = $this->getServiceAccountToken();

        $response = Http::withToken($token)->delete(
            "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}"
        );

        $response->throw();
    }

    /**
     * Active ou désactive un compte Keycloak — synchronisé avec le
     * champ status de l'employé correspondant. Un compte désactivé
     * ne peut plus obtenir de token, quel que soit son mot de passe.
     */
    public function setUserEnabled(string $userId, bool $enabled): void
    {
        $token = $this->getServiceAccountToken();

        $response = Http::withToken($token)->put(
            "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}",
            ['enabled' => $enabled]
        );

        $response->throw();
    }

    /**
     * Développement uniquement (voir garde app()->environment('local')
     * dans createUser()). temporary: false volontairement — pour ne pas
     * avoir à changer le mot de passe à chaque compte de test recréé
     * par un seeder, ce qui serait pénible en boucle de développement.
     */
    private function setDefaultPassword(string $token, string $userId): void
    {
        $response = Http::withToken($token)->put(
            "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}/reset-password",
            [
                'type' => 'password',
                'value' => $this->defaultPassword,
                'temporary' => false,
            ]
        );

        $response->throw();
    }

    private function findUserIdByEmail(string $token, string $email): string
    {
        $response = Http::withToken($token)->get(
            "{$this->baseUrl}/admin/realms/{$this->realm}/users",
            ['email' => $email, 'exact' => 'true']
        );

        $response->throw();

        $users = $response->json();

        if (empty($users)) {
            throw new \RuntimeException("Conflit 409 reçu, mais aucun utilisateur trouvé pour {$email}.");
        }

        return $users[0]['id'];
    }
}
