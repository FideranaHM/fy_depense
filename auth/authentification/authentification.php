<?php
require_once '../../database/config/db_connect.php';
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['mot_de_passe'])) {
    http_response_code(400);
    echo json_encode(["error" => "Champs manquants"]);
    exit();
}

$email = $conn->real_escape_string($data['email']);
$mot_de_passe = $data['mot_de_passe'];

$stmt = $conn->prepare("SELECT id_utilisateur, nom_utilisateur, mot_de_passe FROM utilisateur WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $nom, $hash);
$stmt->fetch();

if ($id && password_verify($mot_de_passe, $hash)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['nom_utilisateur'] = $nom;
    echo json_encode([
        "success" => true,
        "message" => "Connexion réussie"
    ]);

} else {
    http_response_code(401);
    echo json_encode(["error" => "Identifiants invalides"]);
}
?>