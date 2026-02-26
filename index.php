<?php
require 'connexion.php';

$req = mysqli_query($connexion, "SELECT * FROM marche");
$marches = [];
while ($row = mysqli_fetch_assoc($req)) {
    $marches[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des marchés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Nos marchés aux Bénin</h1>
    <div class="row">
        <?php foreach($marches as $marche): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="images/<?php echo htmlspecialchars($marche['image']); ?>" class="card-img-top" height="200" alt="<?php echo htmlspecialchars($marche['nom_marche']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($marche['nom_marche']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($marche['description']); ?></p>
                        <p><strong>Capacité :</strong> <?php echo htmlspecialchars($marche['capacite']); ?> places</p>
                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($marche['adresse']); ?></p>
                        <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($marche['telephone']); ?></p>
                        <a href="formulaire.php?idmarche=<?php echo $marche['idmarche']; ?>" class="btn btn-success">
                        <i class="bi bi-pencil"></i> Modifier
                        </a>

                        <a href="supprimer.php?idmarche=<?php echo $marche['idmarche']; ?>" 
                              class="btn btn-danger"
                              onclick="return confirm('Voulez-vous vraiment supprimer ce marché ?');">
                             <i class="bi bi-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="create.php" class="btn btn-primary">Ajouter un marché</a>
</div>
</body>
</html>
