<?php
declare(strict_types=1);

namespace App\Application\UseCase\Produit;

use App\Infrastructure\Repository\PdoProduitRepository;

final class SupprimerProduitUseCase
{
    public function __construct(private PdoProduitRepository $produitRepo) {}

    public function execute(int $id): bool
    {
        return $this->produitRepo->supprimer($id);
    }
}