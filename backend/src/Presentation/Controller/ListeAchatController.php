<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\CreerListeAchatDTO;
use App\Application\UseCase\ListeAchat\CreerListeAchatUseCase;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repository\PdoListeAchatRepository;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\JwtService; // ← manquant

class ListeAchatController
{
    private CreerListeAchatUseCase $useCase;
    private PdoListeAchatRepository $listeRepo;
    private static string $prefix = '/listesAchat';

    public function __construct()
    {
        $pdo       = PdoConnection::get();
        $userRepo  = new PdoUtilisateurRepository($pdo);
        $this->listeRepo = new PdoListeAchatRepository($pdo);

        $this->useCase = new CreerListeAchatUseCase($userRepo, $this->listeRepo);

    }

    public static function routes(self $controller): array
    {
        $prefix = self::$prefix; // "/listesAchat"

        // helper qui construit la clé "METHOD /prefix/path"
        $r = fn(string $method, string $path, callable $handler) => [
            "$method $prefix$path" => $handler
        ];

        return array_merge(
            $r('GET',  '', [$controller, 'listeAchat']),
            $r('POST', '', [$controller, 'creationListesAchat'])
        );
    }


    public function listeAchat(): void
    {
        try {
            // 1️⃣ Récupère le token pour identifier l’utilisateur
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $token = str_replace('Bearer ', '', $authHeader);
            if (!$token) {
                http_response_code(401);
                echo json_encode([
                    'status'  => 'error',
                    'data'    => null,
                    'message' => null,
                    'erreur'  => 'Token manquant'
                ]);
                return;
            }

            // 2️⃣ Décode le token
            $jwtService = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');
            $payload    = $jwtService->decode($token);
            $userId     = (int) $payload->uid;

            // 3️⃣ Récupère toutes les listes de cet utilisateur
            $listes = $this->listeRepo->trouverParUtilisateur($userId);

            http_response_code(200);
            echo json_encode([
                'status'  => 'success',
                'data'    => $listes, // tableau associatif avec id, utilisateur_id, nom_liste, created_at
                'message' => 'Liste des listes d’achats',
                'erreur'  => null
            ]);

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Créer une liste d’achats (protégée par JWT)
     */
    public function creationListesAchat(): void
    {
        try {
            // 1️⃣ Récupère le token
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $token = str_replace('Bearer ', '', $authHeader);
            if (!$token) {
                http_response_code(401);
                echo json_encode([
                    'status'  => 'error',
                    'data'    => null,
                    'message' => null,
                    'erreur'  => 'Token manquant'
                ]);
                return;
            }

            // 2️⃣ Décode le token
            $jwtService = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');
            $payload    = $jwtService->decode($token);
            $userId     = (int) $payload->uid;

            // 3️⃣ Récupère le nom de la liste depuis le corps de la requête
            $body = json_decode(file_get_contents('php://input'), true);
            $nomListe = $body['nom'] ?? 'Nouvelle liste';

            // 4️⃣ Crée le DTO et exécute le UseCase
            $dto = new CreerListeAchatDTO($userId, $nomListe);
            $id  = $this->useCase->execute($dto);

            // ✅ Réponse structurée
            http_response_code(201);
            echo json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'  => $id,
                    'nom' => $nomListe
                ],
                'message' => 'Liste créée',
                'erreur'  => null
            ]);

        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'data'    => null,
                'message' => null,
                'erreur'  => $e->getMessage()
            ]);
        }
    }


}