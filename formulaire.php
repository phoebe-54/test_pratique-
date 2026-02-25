<?php
require 'connexion.php';

$idmarche = isset($_GET['idmarche']) ? intval($_GET['idmarche']) : 0;
$marche = null;

if ($idmarche > 0) {
    $req = mysqli_query($connexion, "SELECT * FROM marche WHERE idmarche = $idmarche");
    $marche = mysqli_fetch_assoc($req);
    
    // ✅ DEBUG TEMPORAIRE - SUPPRIMEZ APRÈS TEST
    if (!$marche) {
        die("❌ AUCUN MARCHÉ trouvé pour idmarche=$idmarche. Vérifiez phpMyAdmin.<br><a href='index.php'>Retour</a>");
    }
}

if (!$marche) {
    die("❌ Marché #$idmarche introuvable ! <a href='index.php'>Retour</a>");
}

// ✅ TRAITEMENT UPDATE
if ($_POST && isset($_POST['nom_marche'])) {
    $nom_marche = mysqli_real_escape_string($connexion, $_POST['nom_marche']);
    $description = mysqli_real_escape_string($connexion, $_POST['description']);
    $capacite = intval($_POST['capacite']);
    $adresse = mysqli_real_escape_string($connexion, $_POST['adresse']);
    $telephone = mysqli_real_escape_string($connexion, $_POST['telephone']);
    
    $sql = "UPDATE marche SET 
            nom_marche='$nom_marche',
            description='$description',
            capacite=$capacite,
            adresse='$adresse',
            telephone='$telephone'
            WHERE idmarche = $idmarche";
    
    if (mysqli_query($connexion, $sql)) {
        header('Location: index.php?success=1');
        exit();
    } else {
        $error = "❌ Erreur sauvegarde : " . mysqli_error($connexion);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier Marché</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-edit {
            object-fit: cover;
            height: 250px;
            width: 100%;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>✏️ Modifier : <?php echo htmlspecialchars($marche['nom_marche'] ?? 'Marché'); ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <!-- ✅ IMAGE -->
                    <div class="text-center mb-4">
                        <img src="images/<?php echo htmlspecialchars($marche['image'] ?? 'default.jpg'); ?>" 
                             class="card-img-edit shadow-sm" 
                             alt="<?php echo htmlspecialchars($marche['nom_marche'] ?? 'Marché'); ?>">
                    </div>

                    <!-- ✅ FORMULAIRE PRÉ-REMPLI -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom du marché</label>
                            <input type="text" class="form-control" name="nom_marche" 
                                   value="<?php echo htmlspecialchars($marche['nom_marche'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="3" required>
                                <?php echo htmlspecialchars($marche['description'] ?? ''); ?>
                            </textarea>
                        </div>
                        <div class="mb-3">
                             <label>Image</label>
                            <input type="file" name="image" class="form-control">
                         </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Capacité (places)</label>
                                <input type="number" class="form-control" name="capacite" 
                                       value="<?php echo htmlspecialchars($marche['capacite'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="tel" class="form-control" name="telephone" 
                                       value="<?php echo htmlspecialchars($marche['telephone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse</label>
                            <input type="text" class="form-control" name="adresse" 
                                   value="<?php echo htmlspecialchars($marche['adresse'] ?? ''); ?>" required>
                        </div>

                        <!-- ✅ BOUTONS -->
                        <div class="d-flex gap-3 justify-content-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                💾 Sauvegarder les modifications
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                ❌ Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
