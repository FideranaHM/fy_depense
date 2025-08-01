<?php
require_once '../../database/config/db_connect.php'; // adapter selon ton chemin réel

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');

// Vérifier si un ID a été fourni via l’URL (GET)
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID d'achat manquant"]);
    exit;
}

$id = intval($_GET['id']); // Sécuriser l’entrée

// Requête SQL pour supprimer l'achat
$sql = "DELETE FROM achat WHERE id_achat = $id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Achat supprimé avec succès"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Échec de la suppression"]);
}

$conn->close();
