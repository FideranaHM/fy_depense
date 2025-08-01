<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

$id = intval($_GET['id']);
$data = json_decode(file_get_contents("php://input"), true);
$nom = $conn->real_escape_string($data['nom_produit']);

$sql = "UPDATE produit SET nom_produit='$nom' WHERE id_produit=$id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Produit modifié"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Échec modification"]);
}
$conn->close();
