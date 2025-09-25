<?php
declare(strict_types=1);

/* Simule exactement ce que index.php fait */
$raw = file_get_contents('php://input');
echo "=== JSON TEST ===\n";
echo "Raw input : " . var_export($raw, true) . "\n";
echo "Content-Type : " . ($_SERVER['HTTP_CONTENT_TYPE'] ?? 'non défini') . "\n";
echo "Content-Length : " . ($_SERVER['CONTENT_LENGTH'] ?? 'non défini') . "\n";
echo "json_validate() : " . (json_validate($raw) ? 'true' : 'false') . "\n";
$body = json_validate($raw) ? json_decode($raw, true) : null;
echo "Body final : " . var_export($body, true) . "\n";