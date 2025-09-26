<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\ListeAchat;

interface ListeAchatRepositoryInterface
{
    /**
     * Persiste une liste et renvoie son ID auto-incrémenté
     */
    public function sauvegarder(ListeAchat $liste): int;

    /**
     * Renvoie toutes les listes d’un utilisateur (ordre décroissant)
     * @return ListeAchat[]
     */
    public function trouverParUtilisateur(int $userId): array;

    /**
     * Met à jour le nom d’une liste (retourne true si ligne modifiée)
     */
    public function mettreAJour(int $id, string $nom): bool;

    /**
     * Supprime une liste (retourne true si ligne supprimée)
     */
    public function supprimer(int $id): bool;
}