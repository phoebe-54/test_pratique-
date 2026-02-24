<?php
include 'connexion.php';

if (isset($_POST['submit'])) {

    $course_id = (int) $_POST['course_id'];

    if ($course_id > 0) {

        // Mise à jour du statut à "terminee"
        $requete = "UPDATE courses
                    SET statut = 'terminée'
                    WHERE course_id = $course_id AND statut = 'en cours'";

        $execution = mysqli_query($connexion, $requete);

        if ($execution && mysqli_affected_rows($connexion) > 0) {
            header("location: terminer_course.php?success=1");
        } else {
            header("location: terminer_course.php?error=1");
        }

    } else {
        header("location: terminer_course.php?error=1");
    }

} else {
    header("location: terminer_course.php");
}
?>