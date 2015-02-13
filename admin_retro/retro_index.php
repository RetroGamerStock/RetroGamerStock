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
        <h2>Tableau de bord</h2>
			Config site<br/>
			<a href="retro_annonces.php">Valider les annonces</a><br/>
			Créer un article<br/>
			<a href="retro_valider_jeux.php">Valider des jeux</a><br/>
			<a href="retro_modifier_jeux.php">Modifier des jeux</><br/>
			<a href="retro_membres.php">Voir les connexions des membres</a><br/>
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