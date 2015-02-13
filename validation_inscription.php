<?php
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donnée
include('./inc/bibli.php');
if(isset($_GET["x"]))
{
	
	# on vérifie si l'id du membre existe
	$membID = fp_getURL($_GET["x"]);
	$query_select ="SELECT count(*) as numero
					FROM membres
					WHERE membID = '$membID'
					";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$row_select = mysql_fetch_assoc($result_select);
	if(($row_select["numero"]) == 1 )
	{	
		# Dans ce cas on va activer ce membre
		$query_update="UPDATE membres SET membActivation = '1' WHERE membID = '$membID'";
		mysql_query($query_update) or die(mysql_error());
		mysql_close();
		echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",10) </script>';
	}
 
}
else
{
	echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",10) </script>';
}

?>
