<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée"
    ]);
    exit();
}

// Cas : /api/produit/read.php?id=3 → Afficher 1 produit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sécuriser l’entrée

    $sql = "SELECT * FROM produit WHERE id_produit = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        http_response_code(200);
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Produit non trouvé"
        ]);
    }
    $conn->close();
    exit();
}

// Sinon : /api/produit/read.php → Lister tous les produits
$sql = "SELECT * FROM produit";
$result = $conn->query($sql);
$produits = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }
    http_response_code(200);
    echo json_encode($produits);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erreur lors de la récupération des produits"
    ]);
}

$conn->close();
