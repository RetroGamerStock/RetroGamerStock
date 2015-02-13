<?php
session_start();
//Script par Zelix pour Retrogamerstock

//on vérifie si les 2 sessions sont présentes
 require "../php/parametres.php";
 connexion_bd();
  //on va chercher tout ce qui correspond à l'utilisateur
 $affiche = mysql_query("SELECT * FROM membres WHERE membPseudo='".mysql_real_escape_string(stripcslashes($_SESSION['login']))."' AND membType='".mysql_real_escape_string(2)."'");
 $result = mysql_fetch_assoc($affiche);

 //http://php.net/manual/fr/function.extract.php
 extract($result);
 //on libère le résultat de la mémoire
 mysql_free_result($affiche);
if(isset($_SESSION['login']) && isset($_SESSION['mdp']) && $result['membType'] == 2){
	// on teste l'existence de nos variables. On teste également si elles ne sont pas vides
	if (isset($_POST['btnAction'])) {
	$anID = mysql_real_escape_string($_POST["anID"]);
	$membID = $_SESSION["membID"];
	# Si il ne posséde pas la console on l'ajoute dans la table appartient
	$query_update ="UPDATE annonces 
					SET anActivation = '1' 
					WHERE anID ='$anID'
					";
	mysql_query($query_update) or die(mysql_error());
	mysql_close();
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="fr" />
<title>Configuration - <?php echo $nom_site?></title>
<link rel="stylesheet" href="../css/styles.css" type="text/css" media="screen, print, handheld" />
<link rel="shortcut icon" href="../favicon.ico" type="image/vnd.microsoft.icon" />	
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" /><![endif]-->
</head>
 
<body>
		<div id="global">
        	<?php include('../inc/header.php'); ?> 
			<?php include('../inc/menu_admin.php'); ?> 
            <div id="main_admin">
            	<div id="main_content_admin">
        <h2>Validation des annonces</h2>
		<?php if (isset($erreur_ok)) echo $erreur_ok; else if (isset($erreur)) echo $erreur;?>          
            <form action="#" method="post">  
			<?php $query_select="	SELECT * FROM annonces WHERE anActivation = '0'";
			$result_select = mysql_query($query_select) or die(mysql_error());	
			# AFFICHAGE DE LA SELECTION
			#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE
			$rep_img = '../img/membres/annonces/';
			$i = 0;
			if (mysql_num_rows($result_select) == 0)
			{
			echo "aucune annonces à valider";
			}
			while($row_select = mysql_fetch_assoc($result_select)) {
				$i++;
				$anID = $row_select["anID"];
				$anTitre = $row_select["anTitre"];
				//$url = fp_makeURL('macollection.php', $consID);
				$anTexte = $row_select["anTexte"];
				$anDate = substr($row_select["anDate"],6,2)."/".substr($row_select["anDate"],4,2)."/".substr($row_select["anDate"],0,4);
				$anHeure = $row_select["anHeure"];
				$image = $rep_img.$row_select["anID"].'.'.$row_select["anImg"];	
						echo "                <div id='news'>
                    <div class='titre'>
                    	<h2>$anTitre</h2>
                    </div>
                    <div class='corp'>
						<table>
							<tr>
								<td width='200px'>
											<form method = 'POST' action='#'>
												<input type='submit' name='btnAction' value='Valider' class='collec'>
												<input type='hidden' name='anID' value='$anID'>
											</form>
								</td>
								<td width='200px'>
									<img src='$image' alt='' width='100px' height='80px'/>
								</td>
								<td width='400px'>
									$anTexte
								</td>
							</tr>						
						</table>
                    </div>
                    <div class='pied'>

                     Créée le $anDate à $anHeure
                    </div>
                </div>"; 
				}
				?>				
              <br />
              <br />
</form>
		</div>
        </div>
    <?php include('../inc/footer.php');?>
    </div> 
    </div>
</body>
</html>
 <?php
 //fermeture de la BD
 close_bd();
 //on boucle la session du haut de page
}
 else 
 {
  echo 'RIEN A VOIR!!!<script type="text/javascript"> window.setTimeout("location=(\'../index.php\');",3000) </script>'; return false;
 } 
?>