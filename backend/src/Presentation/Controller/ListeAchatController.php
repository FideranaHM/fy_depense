<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\CreerListeAchatDTO;
use App\Application\UseCase\ListeAchat\CreerListeAchatUseCase;
use App\Application\UseCase\ListeAchat\MettreAJourListeAchatUseCase;
use App\Application\UseCase\ListeAchat\SupprimerListeAchatUseCase;
use App\Application\UseCase\ListeAchat\FiltrerListeParDateUseCase;
use App\Application\UseCase\ListeAchat\FiltrerListeParJourUseCase;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Repository\PdoListeAchatRepository;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\JwtService;

class ListeAchatController
{
    private CreerListeAchatUseCase $useCase;
    private PdoListeAchatRepository $listeRepo;
    private MettreAJourListeAchatUseCase $majUC;
    private SupprimerListeAchatUseCase $deleteUC;
    private FiltrerListeParDateUseCase $dateUC;
    private FiltrerListeParJourUseCase $jourUC;
    private JwtService $jwtService;
    private static string $prefix = '/listesAchat';

    public function __construct()
    {
        $pdo       = PdoConnection::get();
        $userRepo  = new PdoUtilisateurRepository($pdo); 
        $this->listeRepo = new PdoListeAchatRepository($pdo);

        $this->useCase = new CreerListeAchatUseCase($userRepo, $this->listeRepo);
        $this->majUC = new MettreAJourListeAchatUseCase($this->listeRepo);
        $this->deleteUC = new SupprimerListeAchatUseCase($this->listeRepo);
        $this->dateUC = new FiltrerListeParDateUseCase($this->listeRepo);
        $this->jourUC = new FiltrerListeParJourUseCase($this->listeRepo);
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
            $r('PUT',  '', [$controller, 'modifierListe']),
            $r('DELETE', '', [$controller, 'supprimerListe']),
            $r('GET', '/date', [$controller, 'filtrerParDate']),
            $r('GET', '/jour', [$controller, 'filtrerParJour'])
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

    public function supprimerListe(): void
    {
        try {
            $userId = $this->getUserIdFromToken();
            $body = $this->getRequestBody();

            $listeId = (int) ($body['id'] ?? 0);
            $confirm = $body['confirm'] ?? false; // Champ pour confirmation

            if ($listeId <= 0) {
                throw new \Exception("ID invalide");
            }

            if (!$confirm) {
                // Demande de confirmation
                $this->jsonResponse([
                    'message' => 'Veuillez confirmer la suppression en envoyant { "id": '.$listeId.', "confirm": true }'
                ], 400);
                return;
            }

            // Vérifie que la liste appartient bien à l'utilisateur
            $liste = $this->listeRepo->trouverParId($listeId);
            if (!$liste || $liste->getUtilisateurId() !== $userId) {
                throw new \Exception("Liste introuvable ou non autorisée");
            }

            // Supprime
            $ok = $this->deleteUC->execute($listeId);
            if (!$ok) {
                throw new \Exception("Aucune ligne supprimée");
            }

            $this->jsonResponse([
                'data'    => ['id' => $listeId],
                'message' => 'Liste supprimée'
            ]);

        } catch (\Throwable $e) {
            $status = $e->getMessage() === 'Token manquant' ? 401 : 400;
            $this->jsonResponse(['erreur' => $e->getMessage()], $status);
        }
    }

    public function filtrerParDate(): void
{
    try {
        $userId = $this->getUserIdFromToken();

        $debutStr = $_GET['debut'] ?? '';
        $finStr   = $_GET['fin']   ?? '';

        if ($debutStr && $finStr) {
            // Intervalle de dates : inclut toute la journée
            $debut = new \DateTime($debutStr . ' 00:00:00');
            $fin   = new \DateTime($finStr . ' 23:59:59');
            $listes = $this->dateUC->execute($userId, $debut, $fin);
        } elseif ($debutStr) {
            // Une seule date → on redirige vers filtrerParJour
            $jour = new \DateTime($debutStr);
            $listes = $this->jourUC->execute($userId, $jour);
        } else {
            // Aucun filtre → toutes les listes
            $listes = $this->listeRepo->trouverParUtilisateur($userId);
        }

        $this->jsonResponse([
            'data'    => $listes,
            'message' => 'Listes filtrées'
        ]);

    } catch (\Throwable $e) {
        $this->jsonResponse(['erreur' => $e->getMessage()], 400);
    }
}

public function filtrerParJour(): void
{
    try {
        $userId = $this->getUserIdFromToken();

        $jourStr = $_GET['jour'] ?? '';
        if ($jourStr === '') {
            throw new \Exception("Paramètre 'jour' manquant (format YYYY-MM-DD attendu)");
        }

        // Intervalle d’un jour complet
        $debut = new \DateTime($jourStr . ' 00:00:00');
        $fin   = new \DateTime($jourStr . ' 23:59:59');
        $listes = $this->dateUC->execute($userId, $debut, $fin);

        $this->jsonResponse([
            'data'    => $listes,
            'message' => "Listes du {$jourStr}"
        ]);

    } catch (\Throwable $e) {
        $this->jsonResponse(['erreur' => $e->getMessage()], 400);
    }
}

    
}
