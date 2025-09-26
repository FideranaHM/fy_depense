<?php
declare(strict_types=1);

namespace App\Application\UseCase\ListeAchat;

use App\Application\DTO\CreerListeAchatDTO;
use App\Domain\Entity\ListeAchat;
use App\Infrastructure\Repository\PdoListeAchatRepository;
use App\Infrastructure\Repository\PdoUtilisateurRepository;

final class CreerListeAchatUseCase
{
    public function __construct(
        private PdoUtilisateurRepository $userRepo,
        private PdoListeAchatRepository $listeRepo
    ) {}

    public function execute(CreerListeAchatDTO $dto): int
{
    $userId = $dto->getUserId();       // ✅ utilise le getter
    $nomListe = $dto->getNomListe();   // ✅ utilise le getter

    // Vérifie que l’utilisateur existe
    if (!$this->userRepo->trouverParId($userId)) {
        throw new \Exception("Utilisateur inconnu");
    }

    // Crée la liste avec le nom passé
    $liste = new ListeAchat(0, $userId, $nomListe, new \DateTime());

    // Sauvegarde dans la base et retourne l'id
    return $this->listeRepo->sauvegarder($liste);
}

}