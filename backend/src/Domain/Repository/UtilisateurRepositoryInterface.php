<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Utilisateur;

interface UtilisateurRepository
{
    public function sauvegarder(Utilisateur $utilisateur): void;
    public function trouverParEmail(string $email): ?Utilisateur;
    public function trouverParId(int $id): ?Utilisateur;
}