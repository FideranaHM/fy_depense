<?php

namespace App\Infrastructure\Service;

use App\Infrastructure\Database\PdoConnection;

class JwtBlacklistService {
    public function __construct(private PdoConnection $pdo) {}

    public function add(string $token, int $exp): void {
        $stmt = $this->pdo->get()->prepare(
            "INSERT INTO jwt_blacklist (token, expires_at) VALUES (:token, :expires_at)"
        );
        $stmt->execute([
            ':token' => $token,
            ':expires_at' => date('Y-m-d H:i:s', $exp)
        ]);
    }

    public function isBlacklisted(string $token): bool {
        $stmt = $this->pdo->get()->prepare(
            "SELECT 1 FROM jwt_blacklist WHERE token = :token"
        );
        $stmt->execute([':token' => $token]);
        return (bool) $stmt->fetchColumn();
    }

    public function cleanup(): void {
        $this->pdo->get()->exec(
            "DELETE FROM jwt_blacklist WHERE expires_at < NOW()"
        );
    }
}