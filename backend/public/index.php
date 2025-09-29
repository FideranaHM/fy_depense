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

/* ---------- 5️⃣ Controllers ---------- */
use App\Presentation\Controller\UtilisateurController;
use App\Presentation\Controller\ListeAchatController;
use App\Presentation\Controller\ProduitController;
use App\Presentation\Controller\AchatController;

$userController = new UtilisateurController();
$listeController = new ListeAchatController();
$produitController = new ProduitController();
$achatController = new AchatController();

/* ---------- 6️⃣ Table de routage ---------- */
$routes = array_merge(
    ['GET /' => function () {
        echo json_encode([
            'status'  => 'success',
            'data'    => null,
            'message' => 'API fy_depense operationnelle',
            'erreur'  => null
        ]);
    }],
    UtilisateurController::routes($userController),
    ListeAchatController::routes($listeController),
    ProduitController::routes($produitController),
    AchatController::routes($achatController)
);


$prefix = '/api'; // Préfixe global pour toutes les routes API
$routesWithPrefix = [];

foreach ($routes as $route => $handler) {
    [$method, $path] = explode(' ', $route, 2);
    $routesWithPrefix["$method $prefix$path"] = $handler;
}

// Remplacer les routes originales par les routes avec préfixe
$routes = $routesWithPrefix;


/* ---------- 7️⃣ Lecture requête ---------- */
$methode = $_SERVER['REQUEST_METHOD'];
$uri     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

error_log("DEBUG: URI demandée = $uri");
error_log("DEBUG: routes dispo = " . implode(", ", array_keys($routes)));

$handler = null;
$params  = [];

foreach ($routes as $pattern => $candidateHandler) {
    [$routeMethod, $routePath] = explode(' ', $pattern, 2);

    if ($routeMethod !== $methode) {
        continue; // mauvaise méthode HTTP
    }

    // Convertit {id} en regex qui capture un segment
    $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $routePath);
    $regex = '#^' . $regex . '$#';

    if (preg_match($regex, $uri, $matches)) {
        array_shift($matches); // le match complet
        $params  = $matches;   // ex: [12]
        $handler = $candidateHandler;
        break;
    }
}

if (!$handler) {
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
try {
    if (is_callable($handler)) { // Closure
        $params=[...$params]; // pour éviter l'avertissemen
        $handler( $params,$body);
    } else { // [Controller, 'methode']
        if (!is_array($handler) || count($handler) !== 2) {
            throw new \RuntimeException('Handler mal formé : doit être [Controller, "methode"]');
        }
        [$controller, $methodeController] = $handler;
        error_log("Appel : " . get_class($controller) . "::$methodeController(" . json_encode($params) . ", " . json_encode($body) . ")");
        $controller->$methodeController($params, $body);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'erreur' => $e->getMessage(),
        'file'   => $e->getFile(),
        'line'   => $e->getLine(),
    ]);
}


/* ----------Listes Achat  ---------- */



