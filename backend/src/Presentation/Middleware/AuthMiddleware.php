<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Infrastructure\Service\JwtService;

final class AuthMiddleware
{
    private JwtService $jwt;

    public function __construct(string $secret)
    {
        $this->jwt = new JwtService($secret);
    }

    /**
     * Vérifie le JWT et injecte userId dans $_SERVER
     * @throws \Exception si invalide
     */
    public function handle(): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            throw new \Exception("Token manquant");
        }

        $payload = $this->jwt->decode($token);

        if (!isset($payload->uid)) {
            throw new \Exception("Token invalide : uid manquant");
        }

        // Injection dans le "contexte global" pour que le contrôleur puisse l’utiliser
        $_SERVER['user_id'] = (int) $payload->uid;
    }
}
