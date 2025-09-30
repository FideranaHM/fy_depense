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
        private string $prenom,
        private string $email,
        private string $password,  // déjà hashé
        private \DateTimeInterface $dateNaissance,
         private string $role = 'user'
    ) {}

    /* ------- Getters (on n’a pas besoin de setters ici) ------- */
    public function getId(): int          { return $this->id; }
    public function getNom(): string      { return $this->nom; }
    public function getPrenom(): string   { return $this->prenom; }
    public function getEmail(): string    { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getDateNaissance(): \DateTimeInterface { return $this->dateNaissance; }
    public function getRole(): string          { return $this->role; }
    public function getAge(): int
    {
        return (new \DateTime())->diff($this->dateNaissance)->y;
    }

     /* Setters (utile pour update) */
    public function setNom(string $nom): void                       { $this->nom = $nom; }
    public function setPrenom(string $prenom): void                 { $this->prenom = $prenom; }
    public function setEmail(string $email): void                   { $this->email = $email; }
    public function setPassword(string $password): void             { $this->password = $password; }
    public function setDateNaissance(\DateTimeInterface $d): void   { $this->dateNaissance = $d; }
    public function setRole(string $role): void                     { $this->role = $role; }


}