<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

$id = intval($_GET['id']);
$sql = "DELETE FROM produit WHERE id_produit=$id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Produit supprimé"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Échec suppression"]);
}
$conn->close();
