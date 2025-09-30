<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Utilisateur;

interface UtilisateurRepositoryInterface
{
    public function sauvegarder(Utilisateur $utilisateur): void;
    public function trouverParEmail(string $email): ?Utilisateur;
    public function trouverParId(int $id): ?Utilisateur;

    /* --- nouvelles méthodes CRUD --- */
    /**
     * @return Utilisateur[]
     */
    public function listerTous(): array;

    public function modifier(Utilisateur $utilisateur): void;

    public function supprimer(int $id): void;

}