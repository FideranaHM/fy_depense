<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\PasswordHasher;

final class RegisterUtilisateurUseCase
{
    public function __construct(
        private PdoUtilisateurRepository $repo,
        private PasswordHasher $hasher
    ) {}

    public function execute(string $nom, string $email, string $passwordBrut): void
    {
        if (trim($email) === '') {
            throw new \Exception("Email vide");
        }

        if ($this->repo->trouverParEmail($email) !== null) {
            throw new \Exception("Email déjà utilisé");
        }

        $hash = $this->hasher->hash($passwordBrut);
        $user = new \App\Domain\Entity\Utilisateur(0, $nom, $email, $hash);
        $this->repo->sauvegarder($user);
    }
}