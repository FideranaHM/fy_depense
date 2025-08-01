<?php
require_once __DIR__ . '/../config/db_connect.php';

$conn->select_db("fy_depense");

$sql = "
CREATE TABLE IF NOT EXISTS liste_achat (
    id_liste INT AUTO_INCREMENT PRIMARY KEY,
    date_liste DATE NOT NULL,
    description TEXT,
    id_utilisateur INT,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
)";
$conn->query($sql);
?>
