<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Vérifier que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée"
    ]);
    exit;
}

// Lire les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier que les champs requis existent
if (!isset($data['nom_produit']) || !isset($data['prix_produit'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Champs nom_produit ou prix_produit manquant"
    ]);
    exit;
}

$nom = $conn->real_escape_string($data['nom_produit']);
$prix = floatval($data['prix_produit']);

// Vérifier si le produit existe déjà
$check_sql = "SELECT id_produit FROM produit WHERE nom_produit = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $nom);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    http_response_code(409); // Conflit
    echo json_encode([
        "success" => false,
        "message" => "Le produit existe déjà"
    ]);
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// Insérer le produit
$sql = "INSERT INTO produit (nom_produit, id_utilisateur) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sd", $nom, $prix);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Produit ajouté avec succès",
        "id_produit" => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erreur lors de l'ajout du produit"
    ]);
}

$stmt->close();
$conn->close();
?>
