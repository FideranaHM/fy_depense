<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['id_achat'], $data['id_produit'], $data['nombre_achat'], 
            $data['unite'], $data['prix_unitaire'], $data['date_achat'], $data['id_liste'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Champs obligatoires manquants"]);
    exit();
}

$id_achat = intval($data['id_achat']);
$id_produit = intval($data['id_produit']);
$nombre_achat = intval($data['nombre_achat']);
$unite = $conn->real_escape_string($data['unite']);
$prix_unitaire = floatval($data['prix_unitaire']);
$date_achat = $data['date_achat'];
$id_liste = intval($data['id_liste']);

$sql = "UPDATE achat 
        SET id_produit=$id_produit, nombre_achat=$nombre_achat, unite='$unite',
            prix_unitaire=$prix_unitaire, date_achat='$date_achat', id_liste=$id_liste
        WHERE id_achat=$id_achat";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Achat mis à jour avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur lors de la mise à jour"]);
}
$conn->close();
