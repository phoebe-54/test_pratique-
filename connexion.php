<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "marchesBenin";

// Connexion à la base de données
$connexion = mysqli_connect($hostname, $username, $password, $database);

// Vérifier la connexion
if (!$connexion) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
?>