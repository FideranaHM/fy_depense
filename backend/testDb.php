<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\Database\PdoConnection;

try {
    $pdo  = PdoConnection::get();
    $stmt = $pdo->query('SELECT DATABASE() AS db');
    $db   = $stmt->fetchColumn();
    echo "Base active : " . $db . PHP_EOL;

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables trouvées : " . implode(', ', $tables) . PHP_EOL;
} catch (\Throwable $e) {
    echo "❌ " . $e->getMessage();
}