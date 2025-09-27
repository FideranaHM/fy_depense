<?php
declare(strict_types=1);

namespace App\Application\UseCase\Produit;

use App\Domain\Entity\Produit;
use App\Infrastructure\Repository\PdoProduitRepository;

final class MettreAJourProduitUseCase
{
    public function __construct(private PdoProduitRepository $produitRepo) {}

    public function execute(int $id, string $nom): bool
    {
        if (trim($nom) === '') {
            throw new \Exception("Nom du produit vide");
        }

        $produit = $this->produitRepo->trouverParId($id);
        if (!$produit) {
            throw new \Exception("Produit introuvable");
        }

        $produit->setNom($nom);
        return $this->produitRepo->mettreAJour($produit);
    }
}