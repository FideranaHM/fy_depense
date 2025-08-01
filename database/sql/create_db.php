<?php
require_once __DIR__ . '/../config/db_connect.php';

$sql = "CREATE DATABASE IF NOT EXISTS fy_depense";
if ($conn->query($sql) === TRUE) {
    echo "Base de données 'fy_depense' créée.<br>";
    $conn->select_db("fy_depense");
} else {
    die("Erreur création DB : " . $conn->error);
}
?>
<!-- si le database ne se cree pas automatiquement veuillez le creer manuellement s'il vous plait son nom doit etre "fy_depense" -->
