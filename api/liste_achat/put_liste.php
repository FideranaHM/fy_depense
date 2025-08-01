<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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
$data = json_decode(file_get_contents("php://input"), true);

$date = $conn->real_escape_string($data['date_liste']);
$desc = $conn->real_escape_string($data['description']);
$id_user = intval($data['id_utilisateur']);

$sql = "UPDATE liste_achat 
        SET date_liste = '$date', description = '$desc', id_utilisateur = $id_user 
        WHERE id_liste = $id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Liste mise à jour"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Erreur lors de la mise à jour"]);
}

$conn->close();
