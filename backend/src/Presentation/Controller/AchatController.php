<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\CreerAchatDTO;
use App\Application\UseCase\Achat\CreerAchatUseCase;
use App\Application\UseCase\Achat\ListerAchatsParListeUseCase;
use App\Application\UseCase\Achat\TrouverAchatParIdUseCase;
use App\Application\UseCase\Achat\MettreAJourAchatUseCase;
use App\Application\UseCase\Achat\SupprimerAchatUseCase;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repository\PdoAchatRepository;
use App\Infrastructure\Repository\PdoProduitRepository;
use App\Infrastructure\Service\JwtService;
use function App\Helpers\Controller\getIdFromUrl;

final class AchatController
{
    private CreerAchatUseCase $creerUC;
    private ListerAchatsParListeUseCase $listerUC;
    private TrouverAchatParIdUseCase $trouverUC;
    private MettreAJourAchatUseCase $majUC;
    private SupprimerAchatUseCase $deleteUC;
    private JwtService $jwtService;
    private static string $prefix = '/achats';

    public function __construct()
    {
        $pdo         = PdoConnection::get();
        $achatRepo   = new PdoAchatRepository($pdo);
        $produitRepo = new PdoProduitRepository($pdo);

        $this->creerUC   = new CreerAchatUseCase($achatRepo, $produitRepo);
        $this->listerUC  = new ListerAchatsParListeUseCase($achatRepo);
        $this->trouverUC = new TrouverAchatParIdUseCase($achatRepo);
        $this->majUC     = new MettreAJourAchatUseCase($achatRepo);
        $this->deleteUC  = new SupprimerAchatUseCase($achatRepo);

        $this->jwtService = new JwtService('secret123'); // adapte ton secret
    }

    /** Routes du controller */
    public static function routes(self $controller): array
    {
        $prefix = self::$prefix;
        $r = fn(string $method, string $path, callable $handler) => ["$method $prefix$path" => $handler];

        return array_merge(
            $r('GET', '', [$controller, 'listerAchats']),
            $r('POST', '', [$controller, 'creerAchat']),
            $r('GET', '/{id}', [$controller, 'trouverAchatParId']),
            $r('PUT', '/{id}', [$controller, 'mettreAJourAchat']),
            $r('DELETE', '/{id}', [$controller, 'supprimerAchat'])
        );
    }

    // -------------------- ACTIONS --------------------

    public function listerAchats(): void
    {
        try {
            /** @var array<int, Achat> $achatsList */
            $achatsList = $this->listerUC->execute(0); // 0 = toutes les listes
            $data = [];

            foreach ($achatsList as $achat) {
                $data[] = [
                    'id'             => $achat->getId(),
                    'liste_achat_id' => $achat->getListeAchatId(),
                    'nom_liste'      => $achat->getNomListe(),
                    'produit_id'     => $achat->getProduitId(),
                    'nom_produit'    => $achat->getNomProduit(),
                    'quantite'       => $achat->getQuantite(),
                    'prix_unitaire'  => $achat->getPrixUnitaire(),
                    'prix_total'     => $achat->getPrixTotal(),
                    'unite'          => $achat->getUnite(),
                    'created_at'     => $achat->getCreatedAt()->format('Y-m-d H:i:s'),
                ];
            }

            $this->jsonResponse([
                'data'    => $data,
                'message' => 'Liste des achats'
            ]);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 500);
        }
    }


    public function creerAchat(): void
    {
        try {
            $body = $this->getRequestBody();
            $dto = new CreerAchatDTO(
                (int)($body['liste_achat_id'] ?? 0),
                (int)($body['produit_id'] ?? 0),
                (int)($body['quantite'] ?? 1),
                (float)($body['prix_unitaire'] ?? 0.0),
                $body['unite'] ?? 'pcs'
            );

            // Crée l'achat et récupère son ID
            $id = $this->creerUC->execute($dto);

            // Récupère l'achat complet
            $achat = $this->trouverUC->execute($id);

            $this->jsonResponse([
                'data' => [
                    'id'             => $achat->getId(),
                    'liste_achat_id' => $achat->getListeAchatId(),
                    'nom_liste'      => $achat->getNomListe(),  // il faut que la méthode existe ou la jointure
                    'produit_id'     => $achat->getProduitId(),
                    'nom_produit'    => $achat->getNomProduit(),
                    'quantite'       => $achat->getQuantite(),
                    'prix_unitaire'  => $achat->getPrixUnitaire(),
                    'unite'          => $achat->getUnite(),
                    'prix_total'     => $achat->getPrixTotal(), // nouveau champ
                ],
                'message' => 'Achat créé'
            ], 201);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
}   


    public function trouverAchatParId(): void
{
    try {
        $id = getIdFromUrl();

        if (!$id || $id <= 0) {
            throw new \Exception("ID invalide");
        }

        $achat = $this->trouverUC->execute($id);
        if (!$achat) throw new \Exception("Achat introuvable");

        $this->jsonResponse([
            'data' => [
                'id'             => $achat->getId(),
                'liste_achat_id' => $achat->getListeAchatId(),
                'produit_id'     => $achat->getProduitId(),
                'quantite'       => $achat->getQuantite(),
                'prix_unitaire'  => $achat->getPrixUnitaire(),
                'unite'          => $achat->getUnite(),
                'created_at'     => $achat->getCreatedAt()->format('Y-m-d H:i:s')
            ],
            'message' => 'Achat trouvé'
        ]);

    } catch (\Throwable $e) {
        $this->jsonResponse(['erreur' => $e->getMessage()], 404);
    }
}


    public function mettreAJourAchat(): void
    {
        try {
            $body         = $this->getRequestBody();
            $id           = getIdFromUrl();
            $quantite     = (int) ($body['quantite'] ?? 0);
            $prixUnitaire = (float) ($body['prix_unitaire'] ?? 0.0);
            $unite        = trim($body['unite'] ?? '');

            if ($id <= 0 || $quantite <= 0 || $prixUnitaire < 0) {
                throw new \Exception("Données invalides");
            }

            $ok = $this->majUC->execute($id, $quantite, $prixUnitaire, $unite);
            if (!$ok) throw new \Exception("Aucune ligne modifiée");

            $this->jsonResponse([
                'data'    => ['id' => $id],
                'message' => 'Achat mis à jour'
            ]);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

    public function supprimerAchat(): void
    {
        try {
            $body = $this->getRequestBody();
            $id   = getIdFromUrl();

            if ($id <= 0) throw new \Exception("ID invalide");

            $ok = $this->deleteUC->execute($id);
            if (!$ok) throw new \Exception("Aucune ligne supprimée");

            $this->jsonResponse([
                'data'    => ['id' => $id],
                'message' => 'Achat supprimé'
            ]);

        } catch (\Throwable $e) {
            $this->jsonResponse(['erreur' => $e->getMessage()], 400);
        }
    }

    // -------------------- MÉTHODES PRIVÉES UTILES --------------------

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
        ], $data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
