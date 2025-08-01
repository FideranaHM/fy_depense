<?php 
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        require_once 'get_achat.php';
        break;
    case 'POST':
        require_once 'post_achat.php';
        break;
    case 'PUT':
        require_once 'put_achat.php';
        break;
    case 'DELETE':
        require_once 'delete_achat.php';
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Méthode non autorisée"]);
        break;
}
?>