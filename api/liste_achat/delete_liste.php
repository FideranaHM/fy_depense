<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

$id = intval($_GET['id']);
$sql = "DELETE FROM liste_achat WHERE id_liste = $id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Liste supprimée"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Erreur suppression"]);
}

$conn->close();
