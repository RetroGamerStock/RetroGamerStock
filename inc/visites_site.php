<?php
//________________________
/**
* Ajouter une visites pour le site
* Paramétres : adresse_IP, Date
*/
//_____________________________________________________________________________
	# Variable
	$adresse_IP = $_SERVER["REMOTE_ADDR"];
	$date=date("Ymd");
	$heure=date("H:i");
	// Cette variable est récupéré depuis la page princicpale 
	//$collID = $_SESSION["membID"];
	
	# Si cette collection n'a été visité par cette adresse IP on ajoute une un record
	$query_select_visites = "SELECT * FROM visites_site WHERE visit_siteIP = '$adresse_IP' AND visit_siteDate = '$date'";
	$result_select_visites = mysql_query($query_select_visites) or die(mysql_error());
	if( mysql_num_rows($result_select_visites) == 0 ){
		$query_insert ="INSERT INTO visites_site (visit_siteID,visit_siteIP,visit_siteDate,visit_siteHeure)
					VALUES ('','$adresse_IP','$date','$heure')
					";
	mysql_query($query_insert) or die(mysql_error());
	}	
?>