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
use App\Application\UseCase\Utilisateur\ListerUtilisateursUseCase;
use App\Application\UseCase\Utilisateur\VoirUtilisateurParIdUseCase;
use App\Application\UseCase\Utilisateur\ModifierUtilisateurUseCase;
use App\Application\UseCase\Utilisateur\SupprimerUtilisateurUseCase;
use App\Application\DTO\UpdateUtilisateurDTO;
use App\Application\UseCase\Utilisateur\ChangerRoleUtilisateurUseCase;
use App\Presentation\Middleware\AdminMiddleware;
use function App\Helpers\Controller\getIdFromUrl;

class UtilisateurController
{
    private RegisterUtilisateurUseCase $registerUC;
    private LoginUtilisateurUseCase $loginUC;
    private LogoutUtilisateurUseCase $logoutUC;
    private ListerUtilisateursUseCase $listerUC;
    private VoirUtilisateurParIdUseCase $voirUC;
    private ModifierUtilisateurUseCase $modifierUC;
    private SupprimerUtilisateurUseCase $supprimerUC;
    private ChangerRoleUtilisateurUseCase $changerRoleUC;

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
        $this->registerUC  = new RegisterUtilisateurUseCase($userRepo, $passwordHasher, $jwtService);
        $this->loginUC     = new LoginUtilisateurUseCase($userRepo, $passwordHasher, $jwtService);
        $this->logoutUC    = new LogoutUtilisateurUseCase($blacklistService, $jwtService);
        $this->listerUC    = new ListerUtilisateursUseCase($userRepo);
        $this->voirUC      = new VoirUtilisateurParIdUseCase($userRepo);
        $this->modifierUC  = new ModifierUtilisateurUseCase($userRepo, $passwordHasher);
        $this->supprimerUC = new SupprimerUtilisateurUseCase($userRepo);
        $this->changerRoleUC = new ChangerRoleUtilisateurUseCase($userRepo);
        

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
            $r('GET',  '',   [$controller, 'lister']),
            $r('GET',  '/{id}',    [$controller, 'voir']),
            $r('PUT',  '/{id}', [$controller, 'modifier']),
            $r('DELETE','/{id}', [$controller, 'supprimer']),
            $r('PATCH', '/role/{id}', [$controller, 'changerRole']),
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
            $dto = new \App\Application\DTO\RegisterUtilisateurDTO(
                $body['nom'],
                $body['prenom'],
                $body['email'],
                $body['password'],
                new \DateTime($body['dateNaissance'])
            );
            
            $result = $this->registerUC->executer($dto);
            
            http_response_code(201);
            echo json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'     => $result['id'],
                    'token'  => $result['token'],
                    'nom'    => $dto->nom,
                    'prenom' => $dto->prenom,
                    'email'  => $dto->email,
                    'dateNaissance' => $dto->dateNaissance->format('Y-m-d'),
                    'age'    => (new \DateTime())->diff($dto->dateNaissance)->y,
                    'role'   => $result['role']
                ],
                'message' => 'Utilisateur créé',
                'erreur'  => null
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'erreur' => $e->getMessage()]);
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

            setcookie('jwt', $token, [
                'expires'  => time() + (3600*3),
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            http_response_code(200);
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

    public function lister(): void
    {
        try {
            $users = $this->listerUC->executer();
            http_response_code(200);
            echo json_encode([
                'status'  => 'success',
                'data'    => array_map(fn(Utilisateur $u) => [
                    'id'    => $u->getId(),
                    'nom'   => $u->getNom(),
                    'prenom'=> $u->getPrenom(),
                    'email' => $u->getEmail(),
                    'dateNaissance' => $u->getDateNaissance()->format('Y-m-d'),
                    'age'   => $u->getAge(),
                    'role'  => $u->getRole(),
                ], $users),
                'message' => null,
                'erreur'  => null
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'data' => null, 'message' => null, 'erreur' => $e->getMessage()]);
        }
    }

    /** Voir un utilisateur par ID */
        public function voir(): void
    {
        $id = getIdFromUrl();
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'erreur' => 'ID manquant ou invalide']);
            return;
        }

        try {
            $u = $this->voirUC->executer($id);
            if (!$u) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'erreur' => 'Utilisateur introuvable']);
                return;
            }
            echo json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'    => $u->getId(),
                    'nom'   => $u->getNom(),
                    'prenom'=> $u->getPrenom(),
                    'email' => $u->getEmail(),
                    'dateNaissance' => $u->getDateNaissance()->format('Y-m-d'),
                    'age'   => $u->getAge(),
                    'role'  => $u->getRole(),
                ],
                'message' => null,
                'erreur'  => null
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'erreur' => $e->getMessage()]);
        }
    }

    /** Modifier un utilisateur */
        public function modifier(): void
    {
        $id = \App\Helpers\Controller\getIdFromUrl();
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'erreur' => 'ID manquant ou invalide']);
            return;
        }

        try {
            $body = json_decode(file_get_contents('php://input'), true);
            $date = isset($body['dateNaissance'])
                ? new \DateTime($body['dateNaissance'])
                : null;
            $dto = new \App\Application\DTO\UpdateUtilisateurDTO(
                $id,
                $body['nom'],
                $body['prenom'],
                $body['email'],
                $body['password'] ?? null,
                $date
            );
            $this->modifierUC->executer($dto);
            $updated = $this->voirUC->executer($id);

            http_response_code(200);
            echo json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'     => $updated->getId(),
                    'nom'    => $updated->getNom(),
                    'prenom' => $updated->getPrenom(),
                    'email'  => $updated->getEmail(),
                    'dateNaissance' => $updated->getDateNaissance()->format('Y-m-d'),
                    'age'    => $updated->getAge(),
                    'role'   => $updated->getRole()
                ],
                'message' => 'Utilisateur modifié',
                'erreur'  => null
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'erreur' => $e->getMessage()]);
        }
    }

    /** Supprimer un utilisateur */
        public function supprimer(): void
    {
        $id = getIdFromUrl();
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'erreur' => 'ID manquant ou invalide']);
            return;
        }

        try {
            $this->supprimerUC->executer($id);
            echo json_encode([
                'status'  => 'success',
                'message' => 'Utilisateur supprimé',
                'erreur'  => null
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'erreur' => $e->getMessage()]);
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
    
    public function changerRole(): void
    {
        $id = getIdFromUrl();
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['erreur' => 'ID manquant']);
            return;
        }

        // Middleware admin
        (new AdminMiddleware(
            new JwtService($_ENV['JWT_SECRET']),
            new PdoUtilisateurRepository(PdoConnection::get())
        ))->handle();

        try {
            $body = json_decode(file_get_contents('php://input'), true);
            $role = $body['role'] ?? '';
            if (!in_array($role, ['user', 'admin'], true)) {
                throw new \RuntimeException('Rôle invalide');
            }

            $useCase = new ChangerRoleUtilisateurUseCase(
                new PdoUtilisateurRepository(PdoConnection::get())
            );
            $useCase->executer($id, $role);

            echo json_encode(['status' => 'success', 'message' => 'Rôle mis à jour']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'erreur' => $e->getMessage()]);
        }
    }
}
