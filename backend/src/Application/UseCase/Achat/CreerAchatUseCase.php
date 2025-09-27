<?php
declare(strict_types=1);

namespace App\Application\UseCase\Achat;

use App\Application\DTO\CreerAchatDTO;
use App\Domain\Entity\Achat;
use App\Domain\Repository\ProduitRepositoryInterface;
use App\Infrastructure\Repository\PdoAchatRepository;

final class CreerAchatUseCase
{
    public function __construct(
        private PdoAchatRepository $achatRepo,
        private ProduitRepositoryInterface $produitRepo
    ) {}

    public function execute(CreerAchatDTO $dto): int
    {
        // Vérifie que le produit existe
        $produit = $this->produitRepo->trouverParId($dto->getProduitId());
        if (!$produit) {
            throw new \Exception("Produit introuvable");
        }

        // Crée l’achat
        $achat = new Achat(
            0,
            $dto->getListeAchatId(),
            $dto->getProduitId(),
            $dto->getQuantite(),
            $dto->getPrixUnitaire(),
            $dto->getUnite(),
            new \DateTime()
        );

        return $this->achatRepo->sauvegarder($achat);
    }
}