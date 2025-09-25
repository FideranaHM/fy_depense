<?php
declare(strict_types=1);

namespace App\Application\UseCase\Migration;

use App\Application\Port\MigrationServiceInterface;

final class RunMigrationUseCase
{
    public function __construct(private MigrationServiceInterface $migration) {}

    public function execute(): void
    {
        $this->migration->migrate();
    }
}