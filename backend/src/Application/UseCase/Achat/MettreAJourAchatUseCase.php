<?php
declare(strict_types=1);

namespace App\Application\UseCase\Achat;

use App\Application\DTO\MettreAJourAchatDTO;
use App\Infrastructure\Repository\PdoAchatRepository;

final class MettreAJourAchatUseCase
{
    public function __construct(private PdoAchatRepository $achatRepo) {}

    public function execute(int $achatId, int $quantite, float $prixUnitaire, string $unite): bool
    {
        return $this->achatRepo->mettreAJour(
            new \App\Domain\Entity\Achat(
                $achatId,
                0, // non utilisé ici
                0, // non utilisé ici
                $quantite,
                $prixUnitaire,
                $unite,
                new \DateTime()
            )
        );
    }
}