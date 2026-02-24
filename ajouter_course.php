<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDO - Ajouter une Course</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

    <div class="fixed-top mb-3">
        <?php include('menu.php'); ?>
    </div>

    <div class="container mt-5 pt-5">
        <h4 class="pt-3 mb-3 text-primary fw-bold">Ajouter une nouvelle Course</h4>

        <!-- Alerte de succes -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Course ajoutee avec succes !
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Alerte d'erreur -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                Une erreur est survenue. Veuillez reessayer.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <form action="tr_ajouter_course.php" method="post" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label fw-bold">Point de Depart</label>
                <input type="text" class="form-control" name="point_depart"
                       placeholder="Ex: Cotonou" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Point d'Arrivee</label>
                <input type="text" class="form-control" name="point_arrivee"
                       placeholder="Ex: Porto-Novo" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Date et Heure prevues</label>
                <input type="datetime-local" class="form-control"
                       name="date_heure" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Image vehicule</label>
                <input type="file" class="form-control"
                       name="image_vehicule" required>
            </div>

            <button type="submit" class="btn btn-primary" name="submit">
                Ajouter la Course
            </button>

        </form>
    </div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>