<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['id_produit'], $data['nombre_achat'], $data['unite'], 
            $data['prix_unitaire'], $data['date_achat'], $data['id_liste'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Données manquantes"]);
    exit();
}

$id_produit = intval($data['id_produit']);
$nombre_achat = intval($data['nombre_achat']);
$unite = $conn->real_escape_string($data['unite']);
$prix_unitaire = floatval($data['prix_unitaire']);
$date_achat = $data['date_achat'];
$id_liste = intval($data['id_liste']);

$sql = "INSERT INTO achat (id_produit, nombre_achat, unite, prix_unitaire, date_achat, id_liste)
        VALUES ($id_produit, $nombre_achat, '$unite', $prix_unitaire, '$date_achat', $id_liste)";

if ($conn->query($sql)) {
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Achat ajouté avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur serveur"]);
}
$conn->close();
