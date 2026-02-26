<?php
require 'connexion.php';

$marche = null;
$message = "";

/* ===== Charger en modification ===== */
if (isset($_GET['idmarche'])) {

    $id = intval($_GET['idmarche']);

    $stmt = mysqli_prepare($connexion, "SELECT * FROM marche WHERE idmarche=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $marche = mysqli_fetch_assoc($result);
}


/* ===== Traitement POST ===== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idmarche   = $_POST['idmarche'] ?? null;
    $nom_marche = $_POST['nom_marche'];
    $description = $_POST['description'];
    $capacite   = $_POST['capacite'];
    $adresse    = $_POST['adresse'];
    $telephone  = $_POST['telephone'];

    $imageName = $marche['image'] ?? "";

    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imageName);
    }

    /* ===== UPDATE ===== */
    if ($idmarche) {
        $stmt = mysqli_prepare($connexion,
            "UPDATE marche 
             SET nom_marche=?, description=?, capacite=?, adresse=?, telephone=?, image=? 
             WHERE idmarche=?"
        );
        mysqli_stmt_bind_param($stmt, "ssisssi",
            $nom_marche, $description, $capacite, $adresse, $telephone, $imageName, $idmarche
        );
        mysqli_stmt_execute($stmt);
        $message = "Marché modifié avec succès !";
    }
    /* ===== INSERT ===== */
    else {
        $stmt = mysqli_prepare($connexion,
            "INSERT INTO marche (nom_marche, description, capacite, adresse, telephone, image)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssisss",
            $nom_marche, $description, $capacite, $adresse, $telephone, $imageName
        );
        mysqli_stmt_execute($stmt);
        $message = "Marché ajouté avec succès !";
    }

    /* Redirection */
    header("Location: index.php?success=" . urlencode($message));
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisie marché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <?= $marche ? "Modifier un marché" : "Ajouter un marché" ?>
            </h4>
        </div>

        <div class="card-body">

            <form action="saisie.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="idmarche" value="<?= $marche['idmarche'] ?? '' ?>">

                <input class="form-control mb-3" type="text" name="nom_marche"
                       placeholder="Nom du marché"
                       value="<?= $marche['nom_marche'] ?? '' ?>" required>

                <textarea class="form-control mb-3" name="description"
                          placeholder="Description"><?= $marche['description'] ?? '' ?></textarea>

                <input class="form-control mb-3" type="number" name="capacite"
                       placeholder="Capacité"
                       value="<?= $marche['capacite'] ?? '' ?>">

                <input class="form-control mb-3" type="text" name="adresse"
                       placeholder="Adresse"
                       value="<?= $marche['adresse'] ?? '' ?>">

                <input class="form-control mb-3" type="text" name="telephone"
                       placeholder="Téléphone"
                       value="<?= $marche['telephone'] ?? '' ?>">

                <input class="form-control mb-3" type="file" name="image">

                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>