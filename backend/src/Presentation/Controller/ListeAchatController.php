<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\CreerListeAchatDTO;
use App\Application\UseCase\ListeAchat\CreerListeAchatUseCase;
use App\Application\UseCase\ListeAchat\MettreAJourListeAchatUseCase;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repository\PdoListeAchatRepository;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\JwtService;

class ListeAchatController
{
    private CreerListeAchatUseCase $useCase;
    private PdoListeAchatRepository $listeRepo;
    private MettreAJourListeAchatUseCase $majUC;
    private JwtService $jwtService;
    private static string $prefix = '/listesAchat';

    public function __construct()
    {
        $pdo       = PdoConnection::get();
        $userRepo  = new PdoUtilisateurRepository($pdo);
        $this->listeRepo = new PdoListeAchatRepository($pdo);

        $this->useCase = new CreerListeAchatUseCase($userRepo, $this->listeRepo);
        $this->majUC = new MettreAJourListeAchatUseCase($this->listeRepo);
        $this->jwtService = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');
    }

    public static function routes(self $controller): array
    {
        $prefix = self::$prefix;
        $r = fn(string $method, string $path, callable $handler) => [
            "$method $prefix$path" => $handler
        ];

        return array_merge(
            $r('GET',  '', [$controller, 'listeAchat']),
            $r('POST', '', [$controller, 'creationListesAchat']),
            $r('PUT',  '', [$controller, 'modifierListe'])
        );
    }

    /** -------------------- MÉTHODES PRIVÉES UTILES -------------------- */

    private function getUserIdFromToken(): int
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);
        if (!$token) {
            $this->jsonResponse(['erreur' => 'Token manquant'], 401);
            exit;
        }

        $payload = $this->jwtService->decode($token);
        return (int) $payload->uid;
    }

    private function getRequestBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode(array_merge([
            'status'  => $statusCode >= 400 ? 'error' : 'success',
            'data'    => null,
            'message' => null,
            'erreur'  => null
        ], $data));
    }

    /** -------------------- ACTIONS -------------------- */

    public function listeAchat(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $listes = $this->listeRepo->trouverParUtilisateur($userId);

            $this->jsonResponse([
                'data'    => $listes,
                'message' => 'Liste des listes d’achats'
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 500);
        }
    }

    public function creationListesAchat(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();
            $nomListe = $body['nom'] ?? 'Nouvelle liste';

            $dto = new CreerListeAchatDTO($userId, $nomListe);
            $id  = $this->useCase->execute($dto);

            $this->jsonResponse([
                'data'    => ['id' => $id, 'nom' => $nomListe],
                'message' => 'Liste créée'
            ], 201);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

    public function modifierListe(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();
            $id = (int) ($_GET['id'] ?? 0);
            $nom = trim($body['nom'] ?? '');

            if ($id <= 0 || $nom === '') {
                throw new \Exception("Données invalides");
            }

            $liste = $this->listeRepo->trouverParId($id);
            if (!$liste || $liste->getUtilisateurId() !== $userId) {
                throw new \Exception("Liste introuvable ou non autorisée");
            }

            $ok = $this->majUC->execute($id, $nom);
            if (!$ok) {
                throw new \Exception("Aucune ligne modifiée");
            }

            $this->jsonResponse([
                'data'    => ['id' => $id, 'nom' => $nom],
                'message' => 'Liste mise à jour'
            ]);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }
}
