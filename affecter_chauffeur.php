<?php
include "connexion.php";

// ============================================
// ✅ ACTIVATION GESTION ERREURS MYSQLI
// ============================================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // ============================================
    // ✅ REQUÊTE 1: Courses EN ATTENTE (CORRIGÉE)
    // ============================================
    // AVANT: Pas de vérification erreur
    // APRÈS: try/catch + vérification résultat
    $req_courses = "SELECT course_id, point_depart, point_arrivee, date_heure
                    FROM courses 
                    WHERE statut = 'en attente' AND chauffeur_id IS NULL
                    ORDER BY date_heure ASC";
    
    $res_courses = mysqli_query($connexion, $req_courses);
    if (!$res_courses) {
        throw new Exception("❌ Erreur courses: " . mysqli_error($connexion));
    }

    // ============================================
    // ✅ REQUÊTE 2: Chauffeurs DISPONIBLES (CORRIGÉE)
    // ============================================
    // ❌ PROBLÈME: prenoms → n'existe PAS dans table
    // ✅ SOLUTION: prenom (singulier) + COALESCE
    $req_chauffeurs = "SELECT chauffeur_id, prenom, nom, telephone
                       FROM chauffeurs 
                       WHERE disponible = 1 
                       ORDER BY nom ASC";
    
    $res_chauffeurs = mysqli_query($connexion, $req_chauffeurs);
    if (!$res_chauffeurs) {
        throw new Exception("❌ Erreur chauffeurs: " . mysqli_error($connexion));
    }

} catch (Exception $e) {
    // ============================================
    // ✅ AFFICHAGE ERREUR DÉTAILLÉ
    // ============================================
    $error_msg = $e->getMessage();
    die("<div class='alert alert-danger mt-5'><h4>🚨 ERREUR BASE DONNÉES</h4>
         <p><strong>Message:</strong> $error_msg</p>
         <p><strong>🔧 VÉRIFIER:</strong></p>
         <ul>
            <li><code>DESCRIBE chauffeurs;</code> → colonne <code>prenom</code> ou <code>prenoms</code> ?</li>
            <li><code>DESCRIBE courses;</code> → colonnes <code>statut</code>, <code>chauffeur_id</code></li>
         </ul></div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDO - 👨‍💼 Affecter un Chauffeur</title>
    <!-- ✅ Bootstrap CDN fiable -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- ============================================
     MENU FIXE
     ============================================ -->
<div class="fixed-top mb-3">
    <?php include('menu.php'); ?>
</div>

<div class="container mt-5 pt-5">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-person-check-fill fs-1 text-primary me-3"></i>
        <h4 class="pt-3 mb-0 text-primary fw-bold">Affecter un Chauffeur</h4>
    </div>

    <!-- ============================================
         ALERTES SUCCÈS/ERREUR (CORRIGÉES)
         ============================================ -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ <strong>Chauffeur affecté avec succès !</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ <strong>Une erreur est survenue.</strong> Réessayez ou contactez l'admin.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ============================================
         FORMULAIRE D'AFFECTATION (SECURISÉ)
         ============================================ -->
    <?php if (mysqli_num_rows($res_courses) > 0 && mysqli_num_rows($res_chauffeurs) > 0): ?>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Associer Course ↔ Chauffeur</h5>
            </div>
            <div class="card-body">
                <form action="tr_affecter_chauffeur.php" method="post">
                    
                    <!-- ✅ SELECT COURSES EN ATTENTE -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">🚩 Course en attente</label>
                        <select class="form-select" name="course_id" required>
                            <option value="">-- Choisir une course --</option>
                            <?php 
                            // 🔄 REMETTRE curseur au début (IMPORTANT!)
                            mysqli_data_seek($res_courses, 0); 
                            while ($course = mysqli_fetch_assoc($res_courses)): 
                            ?>
                                <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                    #<?php echo $course['course_id']; ?> 
                                    <?php echo htmlspecialchars($course['point_depart']); ?> 
                                    → <?php echo htmlspecialchars($course['point_arrivee']); ?>
                                    <small class="text-muted">(<?php echo date('d/m H:i', strtotime($course['date_heure'])); ?>)</small>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- ✅ SELECT CHAUFFEURS DISPONIBLES -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">👨‍💼 Chauffeur disponible</label>
                        <select class="form-select" name="chauffeur_id" required>
                            <option value="">-- Choisir un chauffeur --</option>
                            <?php 
                            // 🔄 REMETTRE curseur au début
                            mysqli_data_seek($res_chauffeurs, 0);
                            while ($ch = mysqli_fetch_assoc($res_chauffeurs)): 
                            ?>
                                <option value="<?php echo htmlspecialchars($ch['chauffeur_id']); ?>">
                                    <?php echo htmlspecialchars(trim($ch['prenom'] . ' ' . $ch['nom'])); ?>
                                    <small class="text-muted">📞 <?php echo htmlspecialchars($ch['telephone']); ?></small>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- ✅ BOUTONS SECURISES -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg"></i> ✅ Affecter le Chauffeur
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">← Retour Courses</a>
                    </div>
                </form>
            </div>
        </div>

    <!-- ============================================
         CASES VIDES - MESSAGES CLAIRS
         ============================================ -->
    <?php elseif (mysqli_num_rows($res_courses) == 0): ?>
        <div class="alert alert-success border-0 text-center">
            <i class="bi bi-check-circle-fill fs-1 text-success mb-3"></i>
            <h4>🎉 Parfait !</h4>
            <p class="mb-0">Toutes les courses sont <strong>affectées ou terminées</strong>.</p>
            <a href="index.php" class="btn btn-outline-success mt-3">Voir toutes les courses</a>
        </div>
        
    <?php else: ?>
        <div class="alert alert-warning border-0 text-center">
            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-3"></i>
            <h4>⏳ Aucun chauffeur disponible</h4>
            <p class="mb-0">Tous les chauffeurs sont actuellement <strong>occupés</strong>.</p>
            <a href="index.php" class="btn btn-outline-warning mt-3">← Retour</a>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap Icons + JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
