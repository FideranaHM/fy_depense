<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class CreerAchatDTO
{
    public function __construct(
        private int    $listeAchatId,
        private int    $produitId,
        private int    $quantite,
        private float  $prixUnitaire,
        private string $unite
    ) {}

    public function getListeAchatId(): int    { return $this->listeAchatId; }
    public function getProduitId(): int       { return $this->produitId; }
    public function getQuantite(): int        { return $this->quantite; }
    public function getPrixUnitaire(): float  { return $this->prixUnitaire; }
    public function getUnite(): string        { return $this->unite; }
}