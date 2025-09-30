<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Utilisateur;
use App\Domain\Repository\UtilisateurRepositoryInterface;
use PDO;

final class PdoUtilisateurRepository implements UtilisateurRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function sauvegarder(Utilisateur $u): void
    {
        $sql = 'INSERT INTO utilisateur (nom, prenom, email, password, date_naissance, role)
                VALUES (:nom, :prenom, :email, :password, :date_naissance, :role)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'            => $u->getNom(),
            ':prenom'         => $u->getPrenom(),
            ':email'          => $u->getEmail(),
            ':password'       => $u->getPassword(),
            ':date_naissance' => $u->getDateNaissance()->format('Y-m-d'),
            ':role'           => $u->getRole(),
        ]);
    }

    public function trouverParEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT id, nom, prenom, email, password, date_naissance, role
                                     FROM utilisateur WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->hydrater($row) : null;
    }

    public function trouverParId(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT id, nom, prenom, email, password, date_naissance, role
                                     FROM utilisateur WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrater($row) : null;
    }

    public function listerTous(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom, prenom, email, password, date_naissance, role
                                   FROM utilisateur ORDER BY id DESC');
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => $this->hydrater($r), $rows);
    }

    public function modifier(Utilisateur $u): void
    {
        $sql = 'UPDATE utilisateur
                SET nom = :nom, prenom = :prenom, email = :email, password = :password,
                    date_naissance = :date_naissance, role = :role
                WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'            => $u->getNom(),
            ':prenom'         => $u->getPrenom(),
            ':email'          => $u->getEmail(),
            ':password'       => $u->getPassword(),
            ':date_naissance' => $u->getDateNaissance()->format('Y-m-d'),
            ':role'           => $u->getRole(),
            ':id'             => $u->getId(),
        ]);
    }

    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM utilisateur WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    private function hydrater(array $row): Utilisateur
    {
        return new Utilisateur(
            (int)$row['id'],
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['password'],
            new \DateTime($row['date_naissance']),
            $row['role']
        );
    }
}