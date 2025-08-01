<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$date = $conn->real_escape_string($data['date_liste']);
$desc = $conn->real_escape_string($data['description']);
$id_user = intval($data['id_utilisateur']);

$sql = "INSERT INTO liste_achat (date_liste, description, id_utilisateur) 
        VALUES ('$date', '$desc', $id_user)";
if ($conn->query($sql)) {
    echo json_encode(["message" => "Liste ajoutée avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Échec d'ajout"]);
}

$conn->close();
