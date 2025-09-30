<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Domain\Entity\Utilisateur;
use App\Infrastructure\Repository\PdoUtilisateurRepository;

class VoirUtilisateurParIdUseCase
{
    public function __construct(private PdoUtilisateurRepository $repo) {}

    public function executer(int $id): ?Utilisateur
    {
        return $this->repo->trouverParId($id);
    }
}