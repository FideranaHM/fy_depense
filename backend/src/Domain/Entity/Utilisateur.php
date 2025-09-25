<?php
declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Objet métier « Utilisateur ».
 * Aucune trace de BDD, de requête, d’API : juste les données
 * et les règles internes (ex. : email non vide).
 */
class Utilisateur
{
    public function __construct(
        private int    $id,       // 0 si pas encore persisté
        private string $nom,
        private string $email,
        private string $password  // déjà hashé
    ) {}

    /* ------- Getters (on n’a pas besoin de setters ici) ------- */
    public function getId(): int       { return $this->id; }
    public function getNom(): string   { return $this->nom; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
}