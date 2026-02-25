<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $capacite = $_POST['capacite'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    // Gestion image
    $imageName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmpName, "images/" . $imageName);

    $sql = "INSERT INTO marche (nom, description, capacite, adresse, telephone, image)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $description, $capacite, $adresse, $telephone, $imageName]);

    header("Location: index.php");
}
?>