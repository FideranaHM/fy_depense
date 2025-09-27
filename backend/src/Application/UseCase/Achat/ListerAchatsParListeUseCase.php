<?php
declare(strict_types=1);

namespace App\Application\UseCase\Achat;

use App\Infrastructure\Repository\PdoAchatRepository;

final class ListerAchatsParListeUseCase
{
    public function __construct(private PdoAchatRepository $achatRepo) {}

    /**
     * @return array[]
     */
    public function execute(int $listeId): array
    {
        return $this->achatRepo->listerParListe($listeId);
    }
}