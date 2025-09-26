<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Infrastructure\Repository\PdoListeAchatRepository;

final class MettreAJourListeAchatUseCase
{
    public function __construct(private PdoListeAchatRepository $listeRepo)
    {
        $this->listeRepo = $listeRepo;
    }

    public function execute(int $listeId, string $nom): bool
    {
        return $this->listeRepo->mettreAJour($listeId, $nom);
    }
}