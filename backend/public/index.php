<?php
declare(strict_types=1);
/**
 * Point d'entrée UNIQUE de l'API REST
 * Chaque requête arrive ici et est redirigée vers le bon contrôleur
 */

/* ---------- 0️⃣ Autoload (Composer) + .env ---------- */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

/* ---------- 0️⃣ bis Timezone ---------- */
date_default_timezone_set($_ENV['TZ'] ?? 'Indian/Antananarivo');

/* ---------- 1️⃣ CORS dynamiques ---------- */
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* ---------- 2️⃣ Connexion PDO (singleton) ---------- */
use App\Infrastructure\Database\PdoConnection;
try {
    $pdo = PdoConnection::get();
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Connexion DB impossible']);
    exit;
}

/* ---------- 3️⃣ Services & Repositories ---------- */
use App\Infrastructure\Service\PasswordHasher;
use App\Infrastructure\Service\JwtService;
use App\Infrastructure\Repository\PdoUtilisateurRepository;

$passwordHasher = new PasswordHasher();
$jwtService     = new JwtService($_ENV['JWT_SECRET'] ?? 'secret');
$userRepo       = new PdoUtilisateurRepository($pdo);

/* ---------- 4️⃣ UseCases ---------- */
use App\Application\UseCase\Utilisateur\RegisterUtilisateurUseCase;
use App\Application\UseCase\Utilisateur\LoginUtilisateurUseCase;

$registerUC = new RegisterUtilisateurUseCase($userRepo, $passwordHasher);
$loginUC    = new LoginUtilisateurUseCase($userRepo, $passwordHasher, $jwtService);

/* ---------- 5️⃣ Controllers ---------- */
use App\Presentation\Controller\UtilisateurController;

$userController = new UtilisateurController($registerUC, $loginUC);

/* ---------- 6️⃣ Table de routage ---------- */
$routes = [
    'GET /' => function () {
        echo json_encode(['msg' => 'API fy_depense opérationnelle']);
    },
    'POST /api/utilisateur/register' => [$userController, 'register'], // ✅ bien l’objet
    'POST /api/utilisateur/login'    => [$userController, 'login'],
];

/* ---------- 7️⃣ Lecture requête ---------- */
$methode = $_SERVER['REQUEST_METHOD'];
$uri     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$key     = $methode . ' ' . $uri;

if (!isset($routes[$key])) {
    http_response_code(404);
    echo json_encode(['erreur' => 'Route introuvable']);
    exit;
}

/* ---------- 8️⃣ Lecture JSON ---------- */
$input = file_get_contents('php://input');
$body  = json_decode($input, true);
if (!is_array($body)) {
    $body = []; // ✅ sécurité : toujours un tableau
}

/* ---------- 9️⃣ Appel de la route ---------- */
$handler = $routes[$key];

try {
    if (is_callable($handler)) {                           // Closure (GET /)
        $handler();
    } else {                                               // [Controller, 'methode']
        if (!is_array($handler) || count($handler) !== 2) {
            throw new \RuntimeException('Handler mal formé : doit être [Controller, "methode"]');
        }
        $controller        = $handler[0];
        $methodeController = $handler[1];
        error_log("Appel : " . get_class($controller) . "::$methodeController(" . json_encode($body) . ")");
        $controller->$methodeController($body);            // ✅ on passe toujours $body
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'erreur' => $e->getMessage(),
        'file'   => $e->getFile(),
        'line'   => $e->getLine(),
    ]);
}
