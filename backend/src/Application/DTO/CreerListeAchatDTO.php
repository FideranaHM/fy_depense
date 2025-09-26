<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class CreerListeAchatDTO
{
    public function __construct(
        private int $userId,
        private string $nomListe
    ) {}

    public function getUserId(): int { return $this->userId; }
    public function getNomListe(): string { return $this->nomListe; }
}