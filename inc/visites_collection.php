<?php
//________________________
/**
* Ajouter une visites pour une collection
* Param�tres : adresse_IP, collID
*/
//_____________________________________________________________________________
	# Variable
	$adresse_IP = $_SERVER["REMOTE_ADDR"];
	$date=date("Ymd");
	$heure=date("H:i");
	// Cette variable est r�cup�r� depuis la page princicpale 
	//$collID = $_SESSION["membID"];
	
	# Si cette collection n'a �t� visit� par cette adresse IP on ajoute une un record
	$query_select_visites = "SELECT * FROM visites_collection WHERE visitIP = '$adresse_IP' AND visitIDColl = '$collID'";
	$result_select_visites = mysql_query($query_select_visites) or die(mysql_error());
	if( mysql_num_rows($result_select_visites) == 0 ){
		$query_insert ="INSERT INTO visites_collection (visitID,visitIP,visitIDColl,visitDate,visitHeure)
					VALUES ('','$adresse_IP','$collID','$date','$heure')
					";
	mysql_query($query_insert) or die(mysql_error());
	}	
?>