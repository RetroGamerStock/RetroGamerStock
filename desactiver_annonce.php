<?php
//________________________
/**
* Page d'accueil du site
*/
//_____________________________________________________________________________
ob_start();			// Buff�risation des sorties
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donn�e
include('inc/bibli.php');
if((isset($_SESSION["membID"]))&&($_SESSION["membActivation"] == 1))
{	
	# Variable
	$anID = mysql_real_escape_string($_POST["anID"]);
	$membID = $_SESSION["membID"];
	# Si il ne poss�de pas la console on l'ajoute dans la table appartient
	$query_update ="UPDATE annonces 
					SET anActivation = '2' 
					WHERE anID ='$anID'
					";
	mysql_query($query_update) or die(mysql_error());
	mysql_close();
	header("location:gerer_annonces.php");
 
}
?>