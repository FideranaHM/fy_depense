<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Infrastructure\Repository\PdoListeAchatRepository;

final class FiltrerListeParDateUseCase
{
    public function __construct(private PdoListeAchatRepository $listeRepo) {}

    /**
     * @return array[] tableau associatif des listes
     */
    public function execute(int $userId, \DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->listeRepo->trouverParUtilisateurEtDate($userId, $debut, $fin);
    }
}