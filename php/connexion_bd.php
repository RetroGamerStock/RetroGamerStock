<?php
//Script par Zelix pour Retrogamerstock
//fonction de connexion à la bd
function connexion_bd(){
 	
	$nom_du_serveur =""; //ton serveur sql
	$nom_de_la_base =""; // ta base de donnée
	$nom_utilisateur =""; // ton nom d'utilisateur sql
	$passe ="";	// ton mot de passe	sql	
	
    $link = mysql_connect ($nom_du_serveur,$nom_utilisateur,$passe) or die ('Erreur : '.mysql_error());
    mysql_select_db($nom_de_la_base, $link) or die ('Erreur :'.mysql_error());
	mysql_set_charset ('UTF8');
    if (!$link) {
        die('Connexion impossible : ' . mysql_error() . "<br/>");
    }
}
function close_bd()
{
    mysql_close();
}
?>