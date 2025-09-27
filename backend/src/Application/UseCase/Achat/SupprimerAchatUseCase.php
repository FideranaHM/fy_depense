<?php
declare(strict_types=1);

namespace App\Application\UseCase\Achat;

use App\Infrastructure\Repository\PdoAchatRepository;

final class SupprimerAchatUseCase
{
    public function __construct(private PdoAchatRepository $achatRepo) {}

    public function execute(int $id): bool
    {
        return $this->achatRepo->supprimer($id);
    }
}