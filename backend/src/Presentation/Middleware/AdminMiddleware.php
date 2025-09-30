<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Infrastructure\Service\JwtService;
use App\Infrastructure\Repository\PdoUtilisateurRepository;

class AdminMiddleware
{
    public function __construct(
        private JwtService $jwt,
        private PdoUtilisateurRepository $repo
    ) {}

    public function handle(): void
    {
        // 1️⃣ Récupérer le token depuis Authorization OU Cookie
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        } else {
            $token = $_COOKIE['jwt'] ?? '';
        }

        // 2️⃣ Vérifier présence du token
        if (empty($token)) {
            http_response_code(401);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => 'Token manquant'
            ]);
            exit;
        }

        // 3️⃣ Décoder le token
        try {
            $payload = $this->jwt->decode($token); // ✅ tableau
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => $e->getMessage()
            ]);
            exit;
        }

        // 4️⃣ Vérifier l'utilisateur en base
        $userId = $payload['uid'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => 'Token sans identifiant utilisateur'
            ]);
            exit;
        }

        $user = $this->repo->trouverParId($userId);

        // 5️⃣ Vérifier rôle admin
        if (!$user || $user->getRole() !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => 'Accès réservé administrateur'
            ]);
            exit;
        }
    }
}
