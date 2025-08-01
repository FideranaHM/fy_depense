<?php
require_once '../../database/config/db_connect.php';
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nom_utilisateur'], $data['email'], $data['mot_de_passe'])) {
    http_response_code(400);
    echo json_encode(["error" => "Champs manquants"]);
    exit();
}

$nom = $conn->real_escape_string($data['nom_utilisateur']);
$email = $conn->real_escape_string($data['email']);
$mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);

// Vérifier si l'email existe déjà
$check = $conn->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Email déjà utilisé"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO utilisateur (nom_utilisateur, email, mot_de_passe) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nom, $email, $mot_de_passe);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
      "success" => true,
      "message" => "Inscription réussie"
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur"]);
}
?>