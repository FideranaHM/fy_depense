<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Utilisateur;
use PDO;

final class PdoUtilisateurRepository
{
    public function __construct(private PDO $pdo) {}

    public function sauvegarder(Utilisateur $user): void
    {
        $sql = 'INSERT INTO utilisateur (nom, email, password) VALUES (:nom, :email, :password)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $user->getNom(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword()
        ]);
    }

    public function trouverParEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT id, nom, email, password FROM utilisateur WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return new Utilisateur(
            (int)$row['id'],
            $row['nom'],
            $row['email'],
            $row['password']
        );
    }

     public function trouverParId(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Utilisateur(
            (int)$row['id'],
            $row['nom'],
            $row['email'],
            $row['password']
        ) : null;
    }
}