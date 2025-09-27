<?php
declare(strict_types=1);

namespace App\Application\UseCase\Achat;

use App\Infrastructure\Repository\PdoAchatRepository;

final class TrouverAchatParIdUseCase
{
    public function __construct(private PdoAchatRepository $achatRepo) {}

    public function execute(int $id): ?\App\Domain\Entity\Achat
    {
        return $this->achatRepo->trouverParId($id);
    }
}