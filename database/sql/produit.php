<?php
require_once __DIR__ . '/../config/db_connect.php';

$conn->select_db("fy_depense");

$sql = "
    CREATE TABLE IF NOT EXISTS produit (
        id_produit INT AUTO_INCREMENT PRIMARY KEY,
        id_utilisateur INT NOT NULL,
        nom_produit VARCHAR(100) NOT NULL,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
    )
";
$conn->query($sql);
?>
