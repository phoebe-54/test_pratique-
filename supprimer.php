<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connexion.php';

if (isset($_GET['idmarche'])) {

    $idmarche = intval($_GET['idmarche']);

    $stmt = mysqli_prepare($connexion, "DELETE FROM marche WHERE idmarche = ?");
    mysqli_stmt_bind_param($stmt, "i", $idmarche);
    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit();
} else {
    echo "Aucun id reçu";
}