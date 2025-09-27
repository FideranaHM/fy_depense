<?php
declare(strict_types=1);

namespace App\Application\UseCase\Produit;

use App\Infrastructure\Repository\PdoProduitRepository;

final class ListerProduitsUseCase
{
    public function __construct(private PdoProduitRepository $produitRepo) {}

    /**
     * @return array[]
     */
    public function execute(): array
    {
        return $this->produitRepo->listerTous();
    }
}