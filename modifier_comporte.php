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
	if($_POST["favori"] == TRUE ){$favori=1;}else{$favori=0;};
	$jeuID = mysql_real_escape_string($_POST["jeuID"]);
	$consID = mysql_real_escape_string($_POST["consID"]);
	$etat = mysql_real_escape_string($_POST["etat"]);
	$exemplaire = mysql_real_escape_string($_POST["exemplaire"]);
	$condition = mysql_real_escape_string($_POST["condition"]);
	$membID = $_SESSION["membID"];
	
	# ON SUPPRIME LE RECORD DE LA TABLE COMPORTE
	$query_update = "UPDATE `comporte`
					SET `nbExemplaire` = '$exemplaire',
					`etat` = '$etat',
					`condition` = '$condition',
					`favori` = '$favori'
					WHERE `compIDCons` = '$consID' 
					AND `compIDJeu` = '$jeuID'
					AND `compIDColl` = '$membID'
					LIMIT 1";
					echo $query_update;
	mysql_query($query_update) or die(mysql_error());
	mysql_close();
	header("location:".  $_SERVER['HTTP_REFERER']."#$jeuID");
 
}
?>