<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Application\DTO\RegisterUtilisateurDTO;
use App\Infrastructure\Service\PasswordHasher;
use App\Domain\Entity\Utilisateur;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\JwtService;

class RegisterUtilisateurUseCase
{
    public function __construct(
        private PdoUtilisateurRepository $repo,
        private PasswordHasher $hasher,
        private JwtService $jwt
    ) {}

    public function executer(RegisterUtilisateurDTO $dto): array
    {
        if ($this->repo->trouverParEmail($dto->email)) {
            throw new \RuntimeException('Email déjà utilisé');
        }

        // Premier utilisateur = admin
        $role = $this->repo->listerTous() ? 'user' : 'admin';


        $hash = $this->hasher->hash($dto->password);
        $user = new Utilisateur(
            0,
            $dto->nom,
            $dto->prenom,
            $dto->email,
            $hash,
            $dto->dateNaissance,
            $role
        );

        $this->repo->sauvegarder($user);
         // 🎯 Génère le token et renvoie token + rôle
        $token = $this->jwt->encode(['uid' => $user->getId()]);
        return ['token' => $token, 'role' => $role];
    }
}