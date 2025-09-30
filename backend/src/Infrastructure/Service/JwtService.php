<?php
declare(strict_types=1);

namespace App\Infrastructure\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtService
{
    public function __construct(private string $secret)
    {
    }

    /**
     * Crée un token JWT (valide 1 h par défaut)
     * $payload = données à encoder (ex. ['uid' => 12])
     */
    public function encode(array $payload): string
    {
        $issuedAt   = time();
        $expire     = $issuedAt + (3 * 3600); // 3 h

        $tokenArray = [
            'iat' => $issuedAt, // issued at
            'exp' => $expire,   // expiration
            'data' => $payload
        ];

        return JWT::encode($tokenArray, $this->secret, 'HS256');
    }

    /**
     * Décode et vérifie un token
     * @return array le payload (toujours en tableau)
     * @throws \Exception si invalide ou expiré
     */
    public function decode(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded->data; // ✅ conversion en tableau
        } catch (\Throwable $e) {
            throw new \Exception("Token invalide ou expiré : " . $e->getMessage());
        }
    }
}