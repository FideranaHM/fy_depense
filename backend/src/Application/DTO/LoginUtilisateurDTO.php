<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class LoginUtilisateurDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}