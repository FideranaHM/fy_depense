#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Application\UseCase\Migration\RunMigrationUseCase;
use App\Infrastructure\Migration\DbMigrationService;
use App\Infrastructure\Database\PdoConnection;

$pdo        = PdoConnection::get();            // connexion sans base
$migration  = new DbMigrationService($pdo);
$useCase    = new RunMigrationUseCase($migration);

try {
    $useCase->execute();
    echo "✅ Base et tables créées ou déjà existantes.\n";
} catch (\Throwable $e) {
    echo "❌ Erreur : " . $e->getMessage() . PHP_EOL;
    exit(1);
}