<?php
include "connexion.php";

// Récupérer seulement les courses "en cours"
$req_en_cours = "SELECT c.course_id, c.point_depart, c.point_arrivee, c.date_heure, c.image_vehicule,
                 CONCAT(ch.prenoms, ' ', ch.nom) AS chauffeur
                 FROM courses c
                 LEFT JOIN chauffeurs ch ON c.chauffeur_id = ch.chauffeur_id
                 WHERE c.statut = 'en cours'
                 ORDER BY c.date_heure ASC";

$res_en_cours = mysqli_query($connexion, $req_en_cours);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDO - Terminer une Course</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

<div class="fixed-top mb-3">
    <?php include('menu.php'); ?>
</div>

<div class="container mt-5 pt-5">
    <h4 class="pt-3 mb-3 text-primary fw-bold">Terminer une Course</h4>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Course marquée comme terminée avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            Une erreur est survenue.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($res_en_cours) > 0): ?>
        <form action="tr_terminer_course.php" method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Sélectionner la Course à terminer</label>
                <select class="form-select" name="course_id" required>
                    <option value="">-- Choisir une course en cours --</option>

                    <?php while ($course = mysqli_fetch_assoc($res_en_cours)): ?>
                        <option value="<?php echo $course['course_id']; ?>">
                            #<?php echo $course['course_id']; ?> -
                            <?php echo htmlspecialchars($course['point_depart']); ?>
                            vers <?php echo htmlspecialchars($course['point_arrivee']); ?> |
                            Chauffeur: <?php echo $course['chauffeur']; ?>
                        </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <button type="submit" class="btn btn-success" name="submit">
                Marquer comme Terminée
            </button>
        </form>

    <?php else: ?>
        <div class="alert alert-info">
            Aucune course en cours pour le moment.
        </div>
    <?php endif; ?>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>