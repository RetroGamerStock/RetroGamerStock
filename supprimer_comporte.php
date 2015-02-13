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
	$membID = $_SESSION["membID"];
	# ON SUPPRIME LE RECORD DE LA TABLE COMPORTE
	$query_delete = "DELETE FROM comporte WHERE compIDCons = '$consID' AND compIDJeu = '$jeuID' AND compIDColl = '$membID'";
	mysql_query($query_delete) or die(mysql_error());
	mysql_close();
	header("location:".  $_SERVER['HTTP_REFERER']);
 
}
?>