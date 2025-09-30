<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class UpdateUtilisateurDTO
{
    public function __construct(
        public int $id,
        public string $nom,
        public string $prenom,
        public string $email,
        public ?string $password = null,
        public ?\DateTimeInterface $dateNaissance = null
    ) {}
}