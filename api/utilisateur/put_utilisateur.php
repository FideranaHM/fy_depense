<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');


if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit();
}

$id = intval($_GET['id']);
$data = json_decode(file_get_contents("php://input"), true);

$nom = $conn->real_escape_string($data['nom_utilisateur'] ?? '');
$email = $conn->real_escape_string($data['email'] ?? '');
$mot_de_passe = isset($data['mot_de_passe']) ? password_hash($data['mot_de_passe'], PASSWORD_BCRYPT) : null;

$set = [];
if ($nom) $set[] = "nom_utilisateur='$nom'";
if ($email) $set[] = "email='$email'";
if ($mot_de_passe) $set[] = "mot_de_passe='$mot_de_passe'";

if (empty($set)) {
    http_response_code(400);
    echo json_encode(["error" => "Aucune donnée à mettre à jour"]);
    exit();
}

$sql = "UPDATE utilisateur SET " . implode(', ', $set) . " WHERE id_utilisateur=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Utilisateur mis à jour"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la mise à jour"]);
}
$conn->close();
