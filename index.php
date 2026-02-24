<?php
include "connexion.php";

// ============================================
// ACTIVATION GESTION ERREURS MYSQLI
// ============================================
// Active les erreurs détaillées pour diagnostiquer les problèmes
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // ============================================
    // REQUÊTE SQL CORRIGÉE
    // ============================================
    // PROBLEME ORIGINAL: ch.prenoms → n'existe pas dans la table chauffeurs
    // SOLUTION: Utilise ch.prenom (nom standard) + COALESCE pour gérer les NULL
    $requete = "SELECT c.course_id, c.point_depart, c.point_arrivee, c.date_heure, c.image_vehicule,
                CONCAT(COALESCE(ch.prenom, ''), ' ', COALESCE(ch.nom, '')) AS chauffeur, 
                c.statut
                FROM courses c
                LEFT JOIN chauffeurs ch ON c.chauffeur_id = ch.chauffeur_id
                ORDER BY c.date_heure DESC";

    // Exécution sécurisée avec vérification
    $execution = mysqli_query($connexion, $requete);
    if (!$execution) {
        throw new Exception("Erreur SQL: " . mysqli_error($connexion));
    }

} catch (Exception $e) {
    // ============================================
    // AFFICHAGE ERREUR DÉTAILLÉ
    // ============================================
    die("<div class='alert alert-danger'><h4>ERREUR BASE DONNÉES</h4>
         <p><strong>Message:</strong> " . $e->getMessage() . "</p>
         <p><strong>Vérifiez:</strong></p>
         <ul>
            <li>Table <code>chauffeurs</code> existe-t-elle ?</li>
            <li>Colonnes <code>prenom</code> et <code>nom</code> existent-elles ? 
                → phpMyAdmin: <code>DESCRIBE chauffeurs;</code></li>
            <li>Table <code>courses</code> existe-t-elle ?</li>
         </ul></div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPIDO - Liste des Courses</title>
    <!-- Bootstrap 5 CDN fiable -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- ============================================
     MENU FIXE EN HAUT
     ============================================ -->
<div class="fixed-top mb-3">
    <?php include('menu.php'); ?>
</div>

<div class="container mt-5 pt-5">
    <h4 class="pt-3 mb-3 text-primary fw-bold">📋 Liste de toutes les Courses</h4>

    <!-- ============================================
         ALERTE SUPPRESSION SUCCÈS
         ============================================ -->
    <?php if (isset($_GET['delete'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Course supprimée avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($execution) > 0): ?>
        <!-- ============================================
             TABLEAU RESPONSIVE
             ============================================ -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover mt-3">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>🚩 Point de Départ</th>
                        <th>🎯 Point d'Arrivée</th>
                        <th>📅 Date/Heure</th>
                        <th>👨‍💼 Chauffeur</th>
                        <th>📊 Statut</th>
                        <th style="width:120px;">🖼️ Image</th>
                        <th style="width:100px;">⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($course = mysqli_fetch_assoc($execution)): ?>
                    <tr>
                        <!-- ID COURSE - htmlspecialchars() obligatoire contre XSS -->
                        <td><strong>#<?php echo htmlspecialchars($course['course_id']); ?></strong></td>
                        
                        <!-- POINTS - Sécurisés avec htmlspecialchars() -->
                        <td><?php echo htmlspecialchars($course['point_depart']); ?></td>
                        <td><?php echo htmlspecialchars($course['point_arrivee']); ?></td>
                        
                        <!-- DATE FORMATÉE - Conversion lisible -->
                        <td>
                            <?php 
                            $date = new DateTime($course['date_heure']);
                            echo $date->format('d/m/Y H:i');
                            ?>
                        </td>
                        
                        <!-- CHAFFEUR - Gestion NULL avec trim() -->
                        <td>
                            <?php 
                            $nomChauffeur = trim($course['chauffeur']);
                            echo $nomChauffeur ? 
                                 htmlspecialchars($nomChauffeur) : 
                                 '<span class="text-muted">👤 Non affecté</span>';
                            ?>
                        </td>
                        
                        <!-- STATUT - Badges Bootstrap -->
                        <td>
                            <?php 
                            $statut = $course['statut'];
                            if ($statut == 'en cours'): ?>
                                <span class="badge bg-warning text-dark">⏳ En cours</span>
                            <?php elseif ($statut == 'terminée'): ?>
                                <span class="badge bg-success">✅ Terminée</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">⏳ En attente</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- ============================================
                             GESTION IMAGE VÉHICULE
                             ============================================ -->
                        <td style="width:120px; height: 60px;">
                            <?php
                            $imagePath = $course['image_vehicule'];
                            if (!empty($imagePath) && file_exists($imagePath)) {
                                // Chemin absolu pour src + sécurisation
                                $src = htmlspecialchars($imagePath);
                                echo '<img src="' . $src . '" 
                                           alt="Véhicule" 
                                           class="img-fluid rounded" 
                                           style="max-width:100px; max-height:50px; object-fit:cover;">';
                            } else {
                                echo '<div class="bg-light rounded p-2 text-center text-muted">
                                        📷 Aucune image
                                      </div>';
                            }
                            ?>
                        </td>
                        
                        <!-- BOUTON SUPPRESSION - Confirmation JS -->
                        <td>
                            <a class="btn btn-danger btn-sm w-100"
                               onclick="return confirm('🗑️ Supprimer la course #<?php echo $course['course_id']; ?> ?\nCette action est irréversible !')"
                               href="supprimer_course.php?id=<?php echo $course['course_id']; ?>">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Stats rapides -->
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5><?php echo mysqli_num_rows($execution); ?></h5>
                        <small>Total Courses</small>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- ============================================
             MESSAGE TABLE VIDE
             ============================================ -->
        <div class="alert alert-info text-center">
            <h5>📭 Aucune course trouvée</h5>
            <p>Commencez par ajouter votre première course !</p>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
