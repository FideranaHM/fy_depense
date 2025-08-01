<?php
require_once '../../database/config/db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM achat WHERE id_achat = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Achat non trouvé"]);
    }
    $conn->close();
    exit();
}

$sql = "SELECT * FROM achat";
$result = $conn->query($sql);
$achats = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $achats[] = $row;
    }
    echo json_encode($achats);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur serveur"]);
}
$conn->close();
