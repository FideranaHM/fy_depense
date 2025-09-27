<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Achat;
use App\Domain\Repository\AchatRepositoryInterface;
use PDO;

final class PdoAchatRepository implements AchatRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    /**
     * Sauvegarde un achat
     */
    public function sauvegarder(Achat $achat): int
    {
        $sql = 'INSERT INTO achat (liste_achat_id, produit_id, quantite, prix_unitaire, unite, created_at)
                VALUES (:liste_id, :produit_id, :quantite, :prix, :unite, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':liste_id'   => $achat->getListeAchatId(),
            ':produit_id' => $achat->getProduitId(),
            ':quantite'   => $achat->getQuantite(),
            ':prix'       => $achat->getPrixUnitaire(),
            ':unite'      => $achat->getUnite(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Trouve un achat par son ID avec noms
     */
    // public function trouverParId(int $id): ?Achat
    // {
    //     $sql = 'SELECT a.id, a.liste_achat_id, a.produit_id, a.quantite, a.prix_unitaire, a.unite, a.created_at,
    //                    p.nom AS produit_nom,
    //                    l.nom_liste AS liste_nom
    //             FROM achat a
    //             JOIN produit p ON a.produit_id = p.id
    //             JOIN liste_achat l ON a.liste_achat_id = l.id
    //             WHERE a.id = :id';
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->execute([':id' => $id]);
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if (!$row) return null;

    //     $prixTotal = (float)$row['quantite'] * (float)$row['prix_unitaire']; // calcul PHP

    //     return new Achat(
    //         (int)$row['id'],
    //         (int)$row['liste_achat_id'],
    //         (int)$row['produit_id'],
    //         (int)$row['quantite'],
    //         (float)$row['prix_unitaire'],
    //         $row['unite'],
    //         new \DateTime($row['created_at']),
    //         $row['liste_nom'],    // nom de la liste
    //         $row['produit_nom'],   // nom du produit
    //         $prixTotal             // prix total (quantité * prix unitaire
    //     );
    // }

    /**
     * Liste tous les achats d'une liste avec noms
     */
    public function listerParListe(int $listeId): array
{
    if ($listeId === 0) {
        // Tous les achats
        $sql = 'SELECT a.id, a.liste_achat_id, a.produit_id, a.quantite, a.prix_unitaire, a.unite, a.created_at,
                       p.nom AS produit_nom,
                       l.nom_liste AS liste_nom
                FROM achat a
                JOIN produit p ON a.produit_id = p.id
                JOIN liste_achat l ON a.liste_achat_id = l.id
                ORDER BY a.created_at DESC';
        $stmt = $this->pdo->query($sql);
    } else {
        // Filtrer par liste spécifique
        $sql = 'SELECT a.id, a.liste_achat_id, a.produit_id, a.quantite, a.prix_unitaire, a.unite, a.created_at,
                       p.nom AS produit_nom,
                       l.nom_liste AS liste_nom
                FROM achat a
                JOIN produit p ON a.produit_id = p.id
                JOIN liste_achat l ON a.liste_achat_id = l.id
                WHERE a.liste_achat_id = :liste_id
                ORDER BY a.created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':liste_id' => $listeId]);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $achats = [];

    foreach ($rows as $row) {
        $prixTotal = (float)$row['quantite'] * (float)$row['prix_unitaire'];

        $achats[] = new Achat(
            (int)$row['id'],
            (int)$row['liste_achat_id'],
            (int)$row['produit_id'],
            (int)$row['quantite'],
            (float)$row['prix_unitaire'],
            $row['unite'],
            new \DateTime($row['created_at']),
            $row['liste_nom'],    // nom de la liste
            $row['produit_nom'],  // nom du produit
            $prixTotal             // prix total
        );
    }

    return $achats;
}


    /**
     * Met à jour un achat
     */
    public function mettreAJour(Achat $achat): bool
    {
        $sql = 'UPDATE achat
                SET quantite = :quantite, prix_unitaire = :prix, unite = :unite
                WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'       => $achat->getId(),
            ':quantite' => $achat->getQuantite(),
            ':prix'     => $achat->getPrixUnitaire(),
            ':unite'    => $achat->getUnite(),
        ]);
    }

    /**
     * Supprime un achat par ID
     */
    public function supprimer(int $id): bool
    {
        $sql = 'DELETE FROM achat WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
