<?php

namespace App\Application\UseCase\Utilisateur;

use App\Infrastructure\Service\JwtBlacklistService;
use App\Infrastructure\Service\JwtService;

class LogoutUtilisateurUseCase {
    public function __construct(
        private JwtBlacklistService $blacklistService,
        private JwtService $jwtService
    ) {}

    public function execute(?string $token): void {
        if (!$token) return;

        // Décoder le token pour récupérer l'expiration
        $payload = $this->jwtService->decode($token);
        if (!$payload) return;

        // Blacklister le token jusqu'à son expiration
        $this->blacklistService->add($token, $payload['exp']);
    }
}