<?php
require_once __DIR__ . '/../config/db_connect.php';
$conn->select_db("fy_depense");

$sql = "
    CREATE TABLE IF NOT EXISTS achat (
        id_achat INT AUTO_INCREMENT PRIMARY KEY,
        id_produit INT NOT NULL,
        nombre_achat INT NOT NULL,
        unite VARCHAR(50), -- Ex: kg, litre, paquet, etc.
        prix_unitaire FLOAT NOT NULL,
        prix_total FLOAT GENERATED ALWAYS AS (nombre_achat * prix_unitaire) STORED,
        date_achat DATE NOT NULL,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_mise_a_jour DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        id_liste INT,
        FOREIGN KEY (id_produit) REFERENCES produit(id_produit),
        FOREIGN KEY (id_liste) REFERENCES liste_achat(id_liste)
    )
";
$conn->query($sql);
?>
