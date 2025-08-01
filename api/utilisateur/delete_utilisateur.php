<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit();
}

$id = intval($_GET['id']);
$sql = "DELETE FROM utilisateur WHERE id_utilisateur = $id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Utilisateur supprimé"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la suppression"]);
}
$conn->close();
