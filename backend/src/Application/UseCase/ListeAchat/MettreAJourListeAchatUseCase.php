<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Domain\Repository\ListeAchatRepositoryInterface;

final class MettreAJourListeAchatUseCase
{
    public function __construct(private ListeAchatRepositoryInterface $listeRepo) {}

    public function execute(int $listeId, string $nom): bool
    {
        return $this->listeRepo->mettreAJour($listeId, $nom);
    }
}