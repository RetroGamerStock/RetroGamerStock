<?php
//Script par Zelix pour Retrogamerstock
//toutes les variable du site

include('connexion_bd.php');
connexion_bd();
$result = mysql_query("SELECT * FROM settings");
while($affiche = mysql_fetch_array($result))
 {
	$nom_site = $affiche['nom_site'];
	$slogan = $affiche['slogan'];
	$site_url = $affiche['site_url'];
	$noreply = $affiche['noreply'];		
	$mail_site = $affiche['mail'];
	$pseudo_admin = $affiche['pseudo_admin'];	
	$keywords = $affiche['keywords'];
	$description = $affiche['description'];
	$robots = $affiche['robots'];
 } 
?>