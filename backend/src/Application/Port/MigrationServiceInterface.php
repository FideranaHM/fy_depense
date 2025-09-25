<?php
declare(strict_types=1);

namespace App\Application\Port;

interface MigrationServiceInterface
{
    public function migrate(): void;
}