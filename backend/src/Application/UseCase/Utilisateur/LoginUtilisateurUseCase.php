<?php
declare(strict_types=1);

namespace App\Application\UseCase\Utilisateur;

use App\Application\DTO\LoginUtilisateurDTO;
use App\Infrastructure\Repository\PdoUtilisateurRepository;
use App\Infrastructure\Service\PasswordHasher;
use App\Infrastructure\Service\JwtService;

final class LoginUtilisateurUseCase
{
    public function __construct(
        private PdoUtilisateurRepository $repo,
        private PasswordHasher $hasher,
        private JwtService $jwt
    ) {}

    public function execute(LoginUtilisateurDTO $dto): string
    {
        $user = $this->repo->trouverParEmail($dto->email);
        if ($user === null) {
            throw new \Exception("Utilisateur introvable");
        } elseif (!$this->hasher->verify($dto->password, $user->getPassword())) {
            throw new \Exception("Mot de passe incorrect");
        }
        return $this->jwt->encode(['uid' => $user->getId()]);
    }
}