<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Infrastructure\Repository\PdoListeAchatRepository;

final class SupprimerListeAchatUseCase
{
    public function __construct(private PdoListeAchatRepository $listeRepo) {}

    public function execute(int $listeId): bool
    {
        return $this->listeRepo->supprimer($listeId);
    }
}