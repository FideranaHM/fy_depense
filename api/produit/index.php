<?php 
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        require_once 'get_produit.php';
        break;
    case 'POST':
        require_once 'post_produit.php';
        break;
    case 'PUT':
        require_once 'put_produit.php';
        break;
    case 'DELETE':
        require_once 'delete_produit.php';
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Méthode non autorisée"]);
        break;
}
?>