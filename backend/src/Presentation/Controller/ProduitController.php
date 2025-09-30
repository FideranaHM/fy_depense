<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\UseCase\Produit\CreerProduitUseCase;
use App\Application\UseCase\Produit\ListerProduitsUseCase;
use App\Application\UseCase\Produit\MettreAJourProduitUseCase;
use App\Application\UseCase\Produit\SupprimerProduitUseCase;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repository\PdoProduitRepository;
use App\Infrastructure\Service\JwtService;

use function App\Helpers\Controller\getIdFromUrl;

final class ProduitController
{
    private CreerProduitUseCase $creerUC;
    private ListerProduitsUseCase $listerUC;
    private MettreAJourProduitUseCase $majUC;
    private SupprimerProduitUseCase $deleteUC;
    private JwtService $jwtService;
    private static string $prefix = '/produits';

    public function __construct()
    {
        $pdo = PdoConnection::get();
        $produitRepo = new PdoProduitRepository($pdo);

        $this->creerUC  = new CreerProduitUseCase($produitRepo);
        $this->listerUC = new ListerProduitsUseCase($produitRepo);
        $this->majUC    = new MettreAJourProduitUseCase($produitRepo);
        $this->deleteUC = new SupprimerProduitUseCase($produitRepo);
        $this->jwtService = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');
    }

    /** -------------------- ROUTES -------------------- */
    public static function routes(self $controller): array
    {
        $prefix = self::$prefix;
        $r = fn(string $method, string $path, callable $handler) => [
            "$method $prefix$path" => $handler
        ];

        return array_merge(
            $r('GET', '', [$controller, 'listerProduits']),
            $r('POST', '', [$controller, 'creerProduit']),
            $r('PUT', '/{id}', [$controller, 'mettreAJourProduit']),
            $r('DELETE', '/{id}', [$controller, 'supprimerProduit'])
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

        $payload = $this->jwtService->decode($token); // maintenant c'est un tableau
        return (int) $payload['uid'];                // ✅ utiliser ['uid'] au lieu de ->uid
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

    public function listerProduits(): void
    {
        try {
            $produits = $this->listerUC->execute();
            $this->jsonResponse([
                'data'    => $produits,
                'message' => 'Liste des produits'
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 500);
        }
    }

    public function creerProduit(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();
            $nom = trim($body['nom'] ?? '');

            if ($nom === '') {
                throw new \Exception("Nom du produit vide");
            }

            $id = $this->creerUC->execute($nom);

            $this->jsonResponse([
                'data'    => ['id' => $id, 'nom' => $nom],
                'message' => 'Produit créé'
            ], 201);
        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

    public function mettreAJourProduit(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();
            $id = getIdFromUrl();
            $nom = trim($body['nom'] ?? '');

            if ($id <= 0 || $nom === '') throw new \Exception("Données invalides");

            $ok = $this->majUC->execute($id, $nom);
            if (!$ok) throw new \Exception("Aucune ligne modifiée");

            $this->jsonResponse([
                'data'    => ['id' => $id, 'nom' => $nom],
                'message' => 'Produit mis à jour'
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

    public function supprimerProduit(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();

            $id = getIdFromUrl();
            $confirm = $body['confirm'] ?? false;

            if ($id <= 0) {
                throw new \Exception("ID invalide");
            }

            if (!$confirm) {
                // Demande confirmation avant suppression
                $this->jsonResponse([
                    'message' => 'Veuillez confirmer la suppression en envoyant { "id": '.$id.', "confirm": true }'
                ], 400);
                return;
            }

            $ok = $this->deleteUC->execute($id);
            if (!$ok) {
                throw new \Exception("Aucune ligne supprimée");
            }

            $this->jsonResponse([
                'data'    => ['id' => $id],
                'message' => 'Produit supprimé'
            ]);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

}
