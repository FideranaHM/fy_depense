<?php
namespace App\Helpers\Controller;

/**
 * Récupère le dernier segment numérique de l'URL
 *
 * @return int|null L'ID trouvé ou null si aucun
 */
function getIdFromUrl(): ?int {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);   // ex: /api/achats/8
    $segments = explode('/', trim($path, '/'));

    $last = end($segments);
    return ctype_digit($last) ? (int)$last : null;
}
