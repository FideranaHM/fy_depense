<?php
declare(strict_types=1);

namespace App\Application\UseCase\Produit;

use App\Domain\Entity\Produit;

use App\Infrastructure\Repository\PdoProduitRepository; 

final class CreerProduitUseCase
{
    public function __construct(private PdoProduitRepository $produitRepo) {}

    public function execute(string $nom): int
    {
        if (trim($nom) === '') {
            throw new \Exception("Nom du produit vide");
        }

        $produit = new Produit(0, $nom);
        return $this->produitRepo->sauvegarder($produit);
    }
}