<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Produit;

interface ProduitRepositoryInterface
{
    public function sauvegarder(Produit $produit): int;
    public function trouverParId(int $id): ?Produit;
    public function listerTous(): array;
    public function mettreAJour(Produit $produit): bool;
    public function supprimer(int $id): bool;
}