<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\LoginUtilisateurDTO;
use App\Application\UseCase\Utilisateur\RegisterUtilisateurUseCase;
use App\Application\UseCase\Utilisateur\LoginUtilisateurUseCase;

class UtilisateurController
{
    public function __construct(
        private RegisterUtilisateurUseCase $registerUC,
        private LoginUtilisateurUseCase $loginUC
    ) {}

    public function register(): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            $this->registerUC->execute(
                $body['nom'] ?? '',
                $body['email'] ?? '',
                $body['password'] ?? ''
            );
            http_response_code(201);
            echo json_encode(['message' => 'Utilisateur créé']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['erreur' => $e->getMessage()]);
        }
    }

    public function login(): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            $dto = new LoginUtilisateurDTO($body['email'] ?? '', $body['password'] ?? '');
            $token = $this->loginUC->execute($dto);
            echo json_encode(['token' => $token]);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['erreur' => $e->getMessage()]);
        }
    }
}