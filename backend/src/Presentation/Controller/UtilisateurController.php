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
use App\Application\UseCase\Utilisateur\LogoutUtilisateurUseCase;
use App\Infrastructure\Service\JwtBlacklistService;

class UtilisateurController
{
    private RegisterUtilisateurUseCase $registerUC;
    private LoginUtilisateurUseCase $loginUC;
    private LogoutUtilisateurUseCase $logoutUC;

    private static string $prefix = '/utilisateur';


    public function __construct()
    {
        // Instanciation interne des dépendances
        $pdo = PdoConnection::get(); // ton singleton PDO
        $userRepo = new PdoUtilisateurRepository($pdo); // Repository concret
        $passwordHasher = new PasswordHasher();     // Service de hash
        $blacklistService = new JwtBlacklistService(new PdoConnection());
        $jwtService     = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');

        // Création des UseCases
        $this->registerUC = new RegisterUtilisateurUseCase($userRepo, $passwordHasher);
        $this->loginUC    = new LoginUtilisateurUseCase($userRepo, $passwordHasher, $jwtService);
         $this->logoutUC   = new LogoutUtilisateurUseCase($blacklistService, $jwtService);
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
            $r('POST', '/login', [$controller, 'login']),
            $r('POST', '/logout', [$controller, 'logout']),
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
        public function logout(): void
    {
        $token = $_COOKIE['jwt'] ?? null;
        $this->logoutUC->execute($token);

        setcookie('jwt', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'success',
            'data'    => null,
            'message' => 'Déconnexion réussie',
            'erreur'  => null
        ]);
        exit;
    }
    
}
