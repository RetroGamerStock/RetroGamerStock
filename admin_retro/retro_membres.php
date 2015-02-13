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
        <h2>Connexion des membres</h2>
		<?php if (isset($erreur_ok)) echo $erreur_ok; else if (isset($erreur)) echo $erreur;?>          
            <form action="#" method="post">  
			<?php $query_select="	SELECT * FROM membres WHERE membActivation = '1' ORDER BY membDateConnection DESC";
			$result_select = mysql_query($query_select) or die(mysql_error());	
			# AFFICHAGE DE LA SELECTION
			#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE
			$i = 0;
			$rep_img = '../img/membres/';
	
			while($row_select = mysql_fetch_assoc($result_select)) {
				$i++;
				$membID = $row_select["membID"];
				$membPseudo = $row_select["membPseudo"];
				//$url = fp_makeURL('macollection.php', $consID);
				$membDateInscription  = substr($row_select["membDateInscription"],6,2)."/".substr($row_select["membDateInscription"],4,2)."/".substr($row_select["membDateInscription"],0,4);
				$DateConnection = substr($row_select["membDateConnection"],6,2)."/".substr($row_select["membDateConnection"],4,2)."/".substr($row_select["membDateConnection"],0,4);
				$Heure = $row_select["membHeureConnection"];
				$image = $rep_img.$row_select["membID"].'.'.$row_select["membImg"];	
				if(!empty($row_select["membImg"]))
				{$image = $rep_img.$row_select['membID'].'.'.$row_select['membImg'];}
				else
				{$image = $rep_img.'0.jpg';}
						echo "         <div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'>          
						<table>
							<tr>
								<td width='200px'>
									<img src='$image' alt='' width='100px' height='80px'/>
								</td>
								<td width='200px'>
									Identifiant :<br/>
									Pseudo :<br/>
									Date d'inscription :<br/>
									Derniére connexion :
								</td>
								<td width='200px'>
									<b>$membID</b><br/>
									<b>$membPseudo</b><br/>
									<b>$membDateInscription </b><br/>
									<b>$DateConnection</b> à <b>$Heure</b>
								</td>
							</tr>									
						</table>
						</div>
                    <div class='pied'>
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