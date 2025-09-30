<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Infrastructure\Repository\PdoUtilisateurRepository;

class ChangerRoleUtilisateurUseCase
{
    public function __construct(private PdoUtilisateurRepository $repo) {}

    public function executer(int $userId, string $nouveauRole): void
    {
        $user = $this->repo->trouverParId($userId);
        if (!$user) throw new \RuntimeException('Utilisateur introuvable');

        $user->setRole($nouveauRole);
        $this->repo->modifier($user);
    }
}