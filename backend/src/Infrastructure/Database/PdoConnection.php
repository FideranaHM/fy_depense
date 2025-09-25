<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class PdoConnection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $name = $_ENV['DB_NAME'] ?? 'fy_depense';
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';

            // Connexion initiale sans DB
            $dsn = "mysql:host=$host;charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                // Tenter de sélectionner la DB si elle existe
                self::$pdo->exec("USE `$name`");

            } catch (PDOException $e) {
                throw new \RuntimeException('PDO : ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
