<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Domain\Repository\ListeAchatRepositoryInterface;

final class ListerListeAchatUseCase
{
    public function __construct(private ListeAchatRepositoryInterface $listeRepo) {}

    /**
     * @return array[] tableau associatif des listes
     */
    public function execute(int $userId): array
    {
        return $this->listeRepo->trouverParUtilisateur($userId);
    }
}