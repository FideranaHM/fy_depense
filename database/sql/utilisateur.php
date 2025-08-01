<?php
require_once __DIR__ . '/../config/db_connect.php';

$conn->select_db("fy_depense");

$sql = "
CREATE TABLE IF NOT EXISTS utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);

?>
