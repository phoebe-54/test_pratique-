<?php
include "connexion.php";

// ============================================
// ✅ 1. GESTION ERREURS + CORRECTION CRITIQUE
// ============================================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // ============================================
    // ✅ 2. REQUÊTE CORRIGÉE : prenoms → prenom
    // ❌ PROBLEME: ch.prenoms n'existe PAS
    // ✅ SOLUTION: ch.prenom + COALESCE pour NULL
    $req_en_cours = "SELECT c.course_id, c.point_depart, c.point_arrivee, c.date_heure, c.image_vehicule,
                            CONCAT(COALESCE(ch.prenom, ''), ' ', COALESCE(ch.nom, '')) AS chauffeur
                     FROM courses c
                     LEFT JOIN chauffeurs ch ON c.chauffeur_id = ch.chauffeur_id
                     WHERE c.statut = 'en cours'
                     ORDER BY c.date_heure ASC";
    
    $res_en_cours = mysqli_query($connexion, $req_en_cours);
    if (!$res_en_cours) {
        throw new Exception("❌ Erreur SQL: " . mysqli_error($connexion));
    }

} catch (Exception $e) {
    // ============================================
    // ✅ 3. DIAGNOSTIC DÉTAILLÉ
    // ============================================
    die("<div class='alert alert-danger mt-5'>
            <h4>🚨 ERREUR BASE DONNÉES</h4>
            <p><strong>Message:</strong> " . $e->getMessage() . "</p>
            <p><strong>🔧 SOLUTION:</strong></p>
            <ul>
                <li>phpMyAdmin → <code>DESCRIBE chauffeurs;</code></li>
                <li>Vérifiez: <code>prenom</code> ou <code>prenoms</code> ?</li>
            </ul>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDO - ✅ Terminer une Course</title>
    <!-- ✅ Bootstrap CDN + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- ============================================
     MENU FIXE
     ============================================ -->
<div class="fixed-top mb-3">
    <?php include('menu.php'); ?>
</div>

<div class="container mt-5 pt-5">
    <!-- ============================================
         TITRE + STATS
         ============================================ -->
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-check-circle-fill fs-1 text-success me-3"></i>
        <div>
            <h4 class="mb-1 text-primary fw-bold">Terminer une Course</h4>
            <small class="text-muted">
                <?php echo mysqli_num_rows($res_en_cours); ?> course(s) en cours
            </small>
        </div>
    </div>

    <!-- ============================================
         ALERTES (AMÉLIORÉES)
         ============================================ -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ <strong>Course terminée avec succès !</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ <strong>Erreur:</strong> Impossible de terminer la course.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ============================================
         FORMULAIRE PRINCIPAL
         ============================================ -->
    <?php if (mysqli_num_rows($res_en_cours) > 0): ?>
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-stopwatch-fill"></i>
                    Courses en cours à terminer
                </h5>
            </div>
            <div class="card-body">
                <form action="tr_terminer_course.php" method="post">
                    
                    <!-- ✅ SELECT COURSE EN COURS -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5">🎯 Choisir la course terminée</label>
                        <select class="form-select form-select-lg" name="course_id" required>
                            <option value="">-- Sélectionner une course --</option>
                            <?php while ($course = mysqli_fetch_assoc($res_en_cours)): ?>
                                <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                    #<?php echo $course['course_id']; ?> 
                                    <?php echo htmlspecialchars($course['point_depart']); ?> 
                                    → <?php echo htmlspecialchars($course['point_arrivee']); ?>
                                    <br>
                                    <small>
                                        👨‍💼 <?php echo htmlspecialchars($course['chauffeur']); ?> | 
                                        📅 <?php echo date('d/m/Y H:i', strtotime($course['date_heure'])); ?>
                                    </small>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- ✅ BOUTONS AMÉLIORÉS -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="submit" class="btn btn-success btn-lg px-4" 
                                name="submit" onclick="return confirm('✅ Marquer cette course comme TERMINÉE ?')">
                            <i class="bi bi-check-lg"></i> Terminer la Course
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-arrow-left"></i> Retour Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>

    <!-- ============================================
         AUCUNE COURSE EN COURS
         ============================================ -->
    <?php else: ?>
        <div class="alert alert-success border-0 text-center py-5">
            <i class="bi bi-check-all fs-1 text-success mb-4"></i>
            <h3 class="text-success">🎉 Excellent !</h3>
            <p class="lead mb-4">Aucune course en cours. Tout est <strong>terminé ou en attente</strong>.</p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="index.php" class="btn btn-success btn-lg">
                    <i class="bi bi-house"></i> Voir Dashboard
                </a>
                <a href="affecter_chauffeur.php" class="btn btn-outline-success btn-lg">
                    ➕ Nouvelle Affectation
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
