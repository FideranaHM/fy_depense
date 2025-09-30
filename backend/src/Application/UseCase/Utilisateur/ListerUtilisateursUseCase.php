<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Infrastructure\Repository\PdoUtilisateurRepository;

class ListerUtilisateursUseCase
{
    public function __construct(private PdoUtilisateurRepository $repo) {}

    /** @return \App\Domain\Entity\Utilisateur[] */
    public function executer(): array
    {
        return $this->repo->listerTous();
    }
}