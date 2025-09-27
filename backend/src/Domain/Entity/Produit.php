<?php
declare(strict_types=1);

namespace App\Domain\Entity;

final class Produit
{
    public function __construct(
        private int    $id,
        private string $nom
    ) {}

    public function getId(): int               { return $this->id; }
    public function getNom(): string           { return $this->nom; }
    public function setNom(string $nom): void  { $this->nom = $nom; }
}