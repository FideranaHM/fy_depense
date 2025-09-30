<?php
declare(strict_types=1);

namespace App\Infrastructure\Migration;

use App\Application\Port\MigrationServiceInterface;
use PDO;

class DbMigrationService implements MigrationServiceInterface
{
    public function __construct(private PDO $pdo) {}

    public function migrate(): void
    {
        $sql = "
            CREATE DATABASE IF NOT EXISTS fy_depense CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            USE fy_depense;

            CREATE TABLE IF NOT EXISTS utilisateur (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(200) NOT NULL UNIQUE,
                password VARCHAR(200) NOT NULL,
                date_naissance DATE NOT NULL,
                role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
            );

            CREATE TABLE IF NOT EXISTS produit (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS liste_achat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                utilisateur_id INT NOT NULL,
                nom_liste VARCHAR(100) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS achat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                liste_achat_id INT NOT NULL,
                produit_id INT NOT NULL,
                quantite INT NOT NULL,
                prix_unitaire DECIMAL(10,2) NOT NULL,
                unite VARCHAR(20) NOT NULL,
                prix_total DECIMAL(10,2) AS (quantite * prix_unitaire) STORED,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (liste_achat_id) REFERENCES liste_achat(id) ON DELETE CASCADE,
                FOREIGN KEY (produit_id) REFERENCES produit(id) ON DELETE CASCADE,
                INDEX idx_created_at (created_at),
                INDEX idx_produit_created_at (produit_id, created_at)
            );
            CREATE TABLE IF NOT EXISTS jwt_blacklist (
                token VARCHAR(65) PRIMARY KEY,
                expires_at DATETIME NOT NULL,
                INDEX idx_expires_at (expires_at)
            );
        ";
        $this->pdo->exec($sql);
    }
}