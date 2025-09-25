<?php
declare(strict_types=1);

namespace App\Infrastructure\Service;

/**
 * Petit service technique : on centralise le hash
 * (si demain on change d’algorithme, on touche qu’ici).
 */
class PasswordHasher
{
    public function hash(string $passwordBrut): string
    {
        // PASSWORD_DEFAULT = bcrypt actuellement
        return password_hash($passwordBrut, PASSWORD_DEFAULT);
    }

    /**
     * Vérifiera un mot de passe en clair vs hash (on l’utilisera pour le login).
     */
    public function verify(string $passwordBrut, string $hash): bool
    {
        return password_verify($passwordBrut, $hash);
    }
}