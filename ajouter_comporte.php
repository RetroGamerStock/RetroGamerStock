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
	# Variable
	$jeuID = mysql_real_escape_string($_POST["jeuID"]);
	$consID = mysql_real_escape_string($_POST["consID"]);
	$collID = $_SESSION["membID"];
	# Si il ne posséde pas la console on l'ajoute dans la table appartient
	$query_select = "SELECT count(*) as nbCons FROM appartient WHERE appartIDCons = '$consID' AND appartIDColl = '$collID'";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$row_select = mysql_fetch_assoc($result_select);
	$nbCons = $row_select["nbCons"];
	if($nbCons == 0){
		$query_insert ="INSERT INTO appartient (appartIDColl,appartIDCons)
					VALUES ('$collID','$consID')
					";
	mysql_query($query_insert) or die(mysql_error());
	}	
	# ICI ON CONSERVE LES VALEURS DE RECHERCHE QUI SERONT REUTILISER LORS DE L'AFFICHAGE DE LA PAGE LUDOTHEQUE
	$_SESSION["txtRechercheJeu"] = mysql_real_escape_string($_POST["txtRechercheJeu"]);
	$_SESSION["txtTriConsole"] = mysql_real_escape_string($_POST["txtTriConsole"]);
	# INSERTION DANS LA TABLE COMPORTE
	$query_insert ="INSERT INTO comporte (compIDColl,compIDCons,compIDJeu)
					VALUES ('$collID','$consID','$jeuID')
					";
	mysql_query($query_insert) or die(mysql_error());
	mysql_close();
	header("location:ludotheque.php#$jeuID");
 
}
?>