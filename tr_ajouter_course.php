
<?php
include 'connexion.php';

if (isset($_POST['submit'])) {

    // Récupération et sécurisation des données du formulaire
    $point_depart  = mysqli_real_escape_string($connexion, trim($_POST['point_depart']));
    $point_arrivee = mysqli_real_escape_string($connexion, trim($_POST['point_arrivee']));
    $date_heure    = mysqli_real_escape_string($connexion, $_POST['date_heure']);
    $image_vehicule = $_FILES['image_vehicule']['name'];

    $extention = explode(".", $image_vehicule);
    $vraiExtension = strtolower(end($extention));
    $tablExt = ['jpg','jpeg','png','gif'];

    if (in_array($vraiExtension, $tablExt))
    {
        $nomFichier = date("Y-m-d")."_".date("H-m-s");
        $vraiNomFichier = $nomFichier.".".$vraiExtension;
        $chemin = "image/".$vraiNomFichier;
        $fichierTemp = $_FILES['image_vehicule']['tmp_name'];
        move_uploaded_file($fichierTemp, $chemin);
    }

    // Vérification que les champs ne sont pas vides
    if (!empty($point_depart) && !empty($point_arrivee) && !empty($date_heure)) {

        // Insertion avec statut "en_attente" par défaut
        $requete = "INSERT INTO courses (point_depart, point_arrivee, date_heure, statut, image_vehicule)
                    VALUES ('$point_depart', '$point_arrivee', '$date_heure', 'en attente', '$chemin')";

        $execution = mysqli_query($connexion, $requete);

        if ($execution) {
            // Redirection avec message de succes
            header("location: ajouter_course.php?success=1");
        } else {
            header("location: ajouter_course.php?error=1");
        }

    } else {
        header("location: ajouter_course.php?error=1");
    }

} else {
    header("location: ajouter_course.php");
}
?>