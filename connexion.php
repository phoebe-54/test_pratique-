<?php
$hostname="localhost";//Adresse du serveur My sql
$username="root";//nom d'utilisation my sql
$password=""; //mot de passe mysql (vide pas defaut avec xampp)
$database="rapido";//nom de la base de donnéé
// connexion à la base de données
$connexion=mysqli_connect( $hostname,$username,$password,$database);
//verification de la connexion
if(!$connexion){
    die("Connexion echouee : ". mysqli_connect_error());
}
?>