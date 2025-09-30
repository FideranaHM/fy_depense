<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Infrastructure\Repository\PdoUtilisateurRepository;

class SupprimerUtilisateurUseCase
{
    public function __construct(private PdoUtilisateurRepository $repo) {}

    public function executer(int $id): void
    {
        $this->repo->supprimer($id);
    }
}