<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Domain\Repository\ListeAchatRepositoryInterface;

final class SupprimerListeAchatUseCase
{
    public function __construct(private ListeAchatRepositoryInterface $listeRepo) {}

    public function execute(int $listeId): bool
    {
        return $this->listeRepo->supprimer($listeId);
    }
}