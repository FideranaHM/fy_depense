<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Produit;
use App\Domain\Repository\ProduitRepositoryInterface;
use PDO;

final class PdoProduitRepository implements ProduitRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function sauvegarder(Produit $produit): int
    {
        $sql = 'INSERT INTO produit (nom) VALUES (:nom)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => $produit->getNom()]);
        return (int) $this->pdo->lastInsertId();
    }

    public function trouverParId(int $id): ?Produit
    {
        $sql = 'SELECT id, nom FROM produit WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Produit((int)$row['id'], $row['nom']);
    }

    public function listerTous(): array
    {
        $sql = 'SELECT id, nom FROM produit ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function mettreAJour(Produit $produit): bool
    {
        $sql = 'UPDATE produit SET nom = :nom WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $produit->getId(), ':nom' => $produit->getNom()]);
    }

    public function supprimer(int $id): bool
    {
        $sql = 'DELETE FROM produit WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}