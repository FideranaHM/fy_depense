<?php
declare(strict_types=1);

namespace App\Domain\Entity;

final class ListeAchat
{
    public function __construct(
        private int    $id,
        private int    $utilisateurId,
        private string $nomListe,
        private \DateTimeInterface $createdAt
    ) {}

    public function getId(): int               { return $this->id; }
    public function getUtilisateurId(): int    { return $this->utilisateurId; }
    public function getNomListe(): string      { return $this->nomListe; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}