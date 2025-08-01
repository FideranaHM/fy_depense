<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nom_utilisateur'], $data['email'], $data['mot_de_passe'])) {
    http_response_code(400);
    echo json_encode(["error" => "Champs obligatoires manquants"]);
    exit();
}

$nom = $conn->real_escape_string($data['nom_utilisateur']);
$email = $conn->real_escape_string($data['email']);
$mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);

$sql = "INSERT INTO utilisateur (nom_utilisateur, email, mot_de_passe)
        VALUES ('$nom', '$email', '$mot_de_passe')";

if ($conn->query($sql)) {
    http_response_code(201);
    echo json_encode(["message" => "Utilisateur créé"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la création"]);
}
$conn->close();
