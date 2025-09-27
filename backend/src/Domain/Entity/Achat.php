<?php
declare(strict_types=1);

namespace App\Domain\Entity;

final class Achat
{
    public function __construct(
        private int    $id,
        private int    $listeAchatId,
        private int    $produitId,
        private int    $quantite,
        private float  $prixUnitaire,
        private string $unite,
        private \DateTimeInterface $createdAt,
        private ?string $nomListe   = null,   // nom de la liste
        private ?string $nomProduit = null,    // nom du produit
        private ?float $prixTotal = null      // <-- ajouté

    ) {}

    public function getId(): int                  { return $this->id; }
    public function getListeAchatId(): int        { return $this->listeAchatId; }
    public function getProduitId(): int           { return $this->produitId; }
    public function getQuantite(): int            { return $this->quantite; }
    public function getPrixUnitaire(): float      { return $this->prixUnitaire; }
    public function getUnite(): string            { return $this->unite; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    // nouveaux getters
    public function getNomListe(): ?string        { return $this->nomListe; }
    public function getNomProduit(): ?string      { return $this->nomProduit; }
    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

}
