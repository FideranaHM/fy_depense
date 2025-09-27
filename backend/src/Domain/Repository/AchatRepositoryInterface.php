<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Achat;

interface AchatRepositoryInterface
{
    public function sauvegarder(Achat $achat): int;
    public function listerParListe(int $listeId): array;
    public function mettreAJour(Achat $achat): bool;
    public function supprimer(int $id): bool;
}