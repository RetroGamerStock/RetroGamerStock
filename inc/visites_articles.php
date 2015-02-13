<?php
//________________________
/**
* Ajouter une visites pour un articles
* Paramétres : adresse_IP, collID
*/
//_____________________________________________________________________________
	# Variable
	$adresse_IP = $_SERVER["REMOTE_ADDR"];
	$date=date("Ymd");
	$heure=date("H:i");
	// Cette variable est récupéré depuis la page princicpale 
	//$collID = $_SESSION["membID"];
	
	# Si cette collection n'a été visité par cette adresse IP on ajoute une un record
	$query_select_visites = "SELECT * FROM vues_article WHERE vueIP = '$adresse_IP' AND vueIDAr = '$arID'";
	$result_select_visites = mysql_query($query_select_visites) or die(mysql_error());
	if( mysql_num_rows($result_select_visites) == 0 ){
		$query_insert ="INSERT INTO vues_article (vueID,vueIP,vueIDAr,vueDate,vueHeure)
					VALUES ('','$adresse_IP','$arID','$date','$heure')
					";
	mysql_query($query_insert) or die(mysql_error());
	}	
?>