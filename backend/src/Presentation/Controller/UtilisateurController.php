<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Domain\Entity\Utilisateur;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\PasswordHasher;
use App\Infrastructure\Service\JwtService;
use App\Application\UseCase\Utilisateur\RegisterUtilisateurUseCase;
use App\Application\UseCase\Utilisateur\LoginUtilisateurUseCase;
use App\Application\DTO\LoginUtilisateurDTO;
use App\Infrastructure\Database\PdoConnection;

class UtilisateurController
{
    private RegisterUtilisateurUseCase $registerUC;
    private LoginUtilisateurUseCase $loginUC;

    private static string $prefix = '/utilisateur';


    public function __construct()
    {
        // Instanciation interne des dépendances
        $pdo = PdoConnection::get(); // ton singleton PDO
        $userRepo = new PdoUtilisateurRepository($pdo); // Repository concret
        $passwordHasher = new PasswordHasher();     // Service de hash
        $jwtService     = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');

        // Création des UseCases
        $this->registerUC = new RegisterUtilisateurUseCase($userRepo, $passwordHasher);
        $this->loginUC    = new LoginUtilisateurUseCase($userRepo, $passwordHasher, $jwtService);
    }

    /**
     * Routes du controller (sans préfixe /api)
     */
    public static function routes(self $controller): array
    {
        $prefix = self::$prefix; // préfixe du controller, ex: '/utilisateur'

        // fonction helper pour préfixer automatiquement
        $r = fn(string $method, string $path, callable $handler) => [
            "$method $prefix$path" => $handler
        ];

        // routes avec chemins relatifs seulement
        return array_merge(
            $r('GET', '/', function () {
                echo json_encode([
                    'status' => 'success',
                    'data' => null,
                    'message' => 'Liste utilisateur',
                    'erreur' => null
                ]);
            }),
            $r('POST', '/register', [$controller, 'register']),
            $r('POST', '/login', [$controller, 'login'])
        );
    }

    /**
     * Enregistrer un utilisateur
     */
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
            echo json_encode([
                'status' => 'success',
                'data' => [ 'email' => $body['email'] ?? '' , 'nom' => $body['nom'] ?? ''],
                'message' => 'Utilisateur créé',
                'erreur' => null
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'data' => null,
                'message' => null,
                'erreur' => $e->getMessage()
            ]);
        }
    }

    /**
     * Connexion d’un utilisateur
     */
    public function login(): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'), true);
            $dto = new LoginUtilisateurDTO($body['email'] ?? '', $body['password'] ?? '');
            $token = $this->loginUC->execute($dto);

            echo json_encode([
                'status' => 'success',
                'data' => ['token' => $token],
                'message' => 'Connexion réussie',
                'erreur' => null
            ]);

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'data' => null,
                'message' => null,
                'erreur' => $e->getMessage()
            ]);
        }
    }
}
