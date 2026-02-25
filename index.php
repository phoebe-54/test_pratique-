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
    <style>
        /* BLocs sur MÊME LIGNE + Bordure BLEU CIEL */
        .card-marche {
            transition: all 0.3s ease;
            border: 2px solid #e3f2fd !important;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .card-marche::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(100,181,246,0.4), transparent);
            transition: left 0.6s;
        }
        .card-marche:hover {
            border-color: #2196f3 !important;
            box-shadow: 0 10px 30px rgba(33,150,243,0.3);
            transform: translateY(-8px);
        }
        .card-marche:hover::before {
            left: 100%;
        }
        .card-marche .card-body {
            flex-grow: 1;
        }
        .card-img-top {
            object-fit: cover !important;
            height: 200px !important;
            width: 100% !important;
        }
        .card-title {
            font-size: 1.25rem !important;
            color: #1976d2 !important;
            min-height: 3rem;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Liste des marchés du Bénin</h2>
    <div class="row g-4">
        <?php if(empty($marches)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Aucun marché trouvé. <a href="create.php">Ajouter le premier !</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($marches as $marche): ?>
                <div class="col-md-4 col-sm-6 col-12 h-100">
                    <div class="card card-marche h-100">
                        <img src="images/<?php echo htmlspecialchars($marche['image'] ?? 'default.jpg'); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($marche['nom_marche'] ?? 'Marché'); ?>">
                        <div class="card-body d-flex flex-column">
                          
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($marche['nom_marche'] ?? 'Sans nom'); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($marche['description'] ?? 'Pas de description'); ?></p>
                            <p class="mb-1"><strong>Capacité :</strong> <?php echo htmlspecialchars($marche['capacite'] ?? 'N/A'); ?> places</p>
                            <p class="mb-1"><strong>Adresse :</strong> <?php echo htmlspecialchars($marche['adresse'] ?? 'N/A'); ?></p>
                            <p class="mb-2"><strong>Téléphone :</strong> <?php echo htmlspecialchars($marche['telephone'] ?? 'N/A'); ?></p>
                            
                            <!-- Boutons ALIGNÉS EN BAS -->
                            <div class="mt-auto">
                              
                               <a href="formulaire.php?idmarche=<?php echo ($marche['idmarche'] ?? 0); ?>" class="btn btn-success btn-sm me-2">Modifier</a>

                               
                                <a href="delete.php?idmarche=<?php echo ($marche['idmarche'] ?? 0); ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('⚠️ Supprimer « <?php echo htmlspecialchars($marche['nom_marche'] ?? 'Inconnu'); ?> » ?');">
                                   Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>  <!-- ✅ FERMETURE CORRIGÉE -->
    </div>  <!-- ✅ FERMETURE ROW CORRIGÉE -->
    <div class="text-center mt-4">
        <a href="create.php" class="btn btn-primary btn-lg">➕ Ajouter un marché</a>
    </div>
</div>
</body>
</html>
