<?php
require_once '../../database/config/db_connect.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id_utilisateur, nom_utilisateur, email, date_creation 
            FROM utilisateur WHERE id_utilisateur = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Utilisateur non trouvé"]);
    }
    $conn->close();
    exit();
}

$sql = "SELECT id_utilisateur, nom_utilisateur, email, date_creation FROM utilisateur";
$result = $conn->query($sql);
$utilisateurs = [];

while ($row = $result->fetch_assoc()) {
    $utilisateurs[] = $row;
}
echo json_encode($utilisateurs);
$conn->close();
