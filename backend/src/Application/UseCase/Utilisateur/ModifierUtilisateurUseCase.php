<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Application\DTO\UpdateUtilisateurDTO;
use App\Infrastructure\Service\PasswordHasher;
use App\Domain\Entity\Utilisateur;
use App\Infrastructure\Repository\PdoUtilisateurRepository;

class ModifierUtilisateurUseCase
{
    public function __construct(
        private PdoUtilisateurRepository $repo,
        private PasswordHasher $hasher
    ) {}

    public function executer(UpdateUtilisateurDTO $dto): Utilisateur
    {
        $old = $this->repo->trouverParId($dto->id);
        if (!$old) throw new \RuntimeException('Utilisateur introuvable');

        $pwd = ($dto->password !== null && $dto->password !== '')
            ? $this->hasher->hash($dto->password)
            : $old->getPassword();

        $date = $dto->dateNaissance ?? $old->getDateNaissance();

        $updated = new Utilisateur(
            $old->getId(),
            $dto->nom,
            $dto->prenom,
            $dto->email,
            $pwd,
            $date,
            $old->getRole()
        );

        $this->repo->modifier($updated);
         return $updated; 
    }
}