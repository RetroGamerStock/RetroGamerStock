<?php
//________________________
/**
* Page d'accueil du site
*/
//_____________________________________________________________________________
ob_start();			// Bufférisation des sorties
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donnée
include('inc/bibli.php');

if((isset($_SESSION["membID"]))&&($_SESSION["membActivation"] == 1))
{
	# ICI ON CONSERVE LES VALEURS DE RECHERCHE QUI SERONT REUTILISER LORS DE L'AFFICHAGE DE LA PAGE LUDOTHEQUE
	$_SESSION["txtRechercheConsole"] = mysql_real_escape_string($_POST["txtRechercheConsole"]);
	$consID = mysql_real_escape_string($_POST["consID"]);
	$collID = $_SESSION["membID"];
	$query_insert ="INSERT INTO appartient (appartIDColl,appartIDCons)
					VALUES ('$collID','$consID')
					";
	mysql_query($query_insert) or die(mysql_error());
	header("location:ludotheque.php");
 
}
?>