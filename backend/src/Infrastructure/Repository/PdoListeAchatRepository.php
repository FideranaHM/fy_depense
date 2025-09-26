<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ListeAchat;
use PDO;

final class PdoListeAchatRepository
{
    public function __construct(private PDO $pdo) {}

    public function sauvegarder(ListeAchat $liste): int
    {
        $sql = 'INSERT INTO liste_achat (utilisateur_id, nom_liste, created_at) VALUES (:uid, :nom, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $liste->getUtilisateurId(),
            ':nom' => $liste->getNomListe(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function trouverParUtilisateur(int $userId): array
    {
        $sql = 'SELECT id, utilisateur_id, nom_liste, created_at FROM liste_achat WHERE utilisateur_id = :uid ORDER BY created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function trouverParId(int $id): ?ListeAchat
    {
        $sql = 'SELECT id, utilisateur_id, nom_liste, created_at FROM liste_achat WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new \App\Domain\Entity\ListeAchat(
            (int) $row['id'],
            (int) $row['utilisateur_id'],
            $row['nom_liste'],
            new \DateTime($row['created_at'])
        );
    }

    public function modifier(int $id, string $nom): bool
    {
        $sql = 'UPDATE liste_achat SET nom_liste = :nom WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':nom' => $nom]);
    }

    public function supprimer(int $id): bool
    {
        $sql = 'DELETE FROM liste_achat WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}