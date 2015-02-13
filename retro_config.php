<?php
session_start();
//Script par Zelix pour Retrogamerstock

//on vérifie si les 2 sessions sont présentes
 require "../php/parametres.php";
 connexion_bd();
  //on va chercher tout ce qui correspond à l'utilisateur
 $affiche = mysql_query("SELECT * FROM membres WHERE pseudo='".mysql_real_escape_string(stripcslashes($_SESSION['login']))."' AND types='".mysql_real_escape_string(2)."'");
 $result = mysql_fetch_assoc($affiche);

 //http://php.net/manual/fr/function.extract.php
 extract($result);
 //on libère le résultat de la mémoire
 mysql_free_result($affiche);
if(isset($_SESSION['login']) && isset($_SESSION['mdp']) && $result['types'] == 2){
	// on teste l'existence de nos variables. On teste également si elles ne sont pas vides
	if (isset($_POST['Valider'])) {
		
		if(empty($_POST['nom_site'])){
			$erreur = '<div id="erreur">Entre un nom de site</div>';
		}
		else if(empty($_POST['slogan'])){
			$erreur = '<div id="erreur">Entre un slogan</div>';
		}	
		else if(empty($_POST['url_site'])){
			$erreur = '<div id="erreur">Entre l\'url de ton site</div>';
		}							 
        else if (!(filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL))) {
         $erreur = '<div class="erreur"><a name="ok" style="text-decoration:none;color:white">Adresse mail au mauvais format.</div>';
        } 
        else if (!(filter_var($_POST['noreply'], FILTER_VALIDATE_EMAIL))) {
         $erreur = '<div class="erreur"><a name="ok" style="text-decoration:none;color:white">Adresse mail au mauvais format.</div>';
        } 	
		else if(empty($_POST['pseudo_admin'])){
			$erreur = '<div id="erreur">Entre ton pseudo</div>';
		}		
		else{
	$modif = 'UPDATE settings SET nom_site="'.mysql_escape_string($_POST['nom_site']).'", 
							  slogan="'.mysql_escape_string($_POST['slogan']).'", 
							  site_url="'.mysql_escape_string($_POST['url_site']).'", 
							  noreply="'.mysql_escape_string($_POST['noreply']).'",
							  mail="'.mysql_escape_string($_POST['mail']).'"
							  pseudo_admin="'.mysql_escape_string($_POST['pseudo_admin']).'"';   
								  
		if (!$modif) {
		 die('Requête invalide : ' . mysql_error());
		}
		else{
			$erreur_ok ='<div class="ok">Mise à jour éffectué avec succès. Redirection en cours...</div><script type="text/javascript"> window.setTimeout("location=(\'retro_index.php\');",3000) </script>';		
			} 
	}
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
		<?php include('js/javascript.js'); ?>
		<?php include('js/css_javascript.js'); ?>
</head>
 
<body>
		<div id="global">
        	<?php include('../inc/header.php'); ?> 
			<?php include('../inc/menu_admin.php'); ?> 
            <div id="main_admin">
            	<div id="main_content_admin">
        <h2>Configuration du site</h2>
		<?php if (isset($erreur_ok)) echo $erreur_ok; else if (isset($erreur)) echo $erreur;?>          
            <form action="#" method="post">  
              <fieldset class="info_connexion"> 
              <legend>Informations de connexion</legend>   
              		  <div style="margin-bottom:10px">          
                      <label>Pseudo admin:</label>
                      <input class="inscription" name="pseudo_admin" type="text" value="<?php echo $pseudo_admin; ?>" size="40">
					  </div>
                      <br clear="all">                                                
              		  <div style="margin-bottom:2px">          
                      <label>Nom du site :</label>
                      <input class="inscription" name="nom_site" type="text" value="<?php echo $nom_site; ?>" size="40">
					  </div>
                      <br clear="all">
                      <div style="margin-bottom:2px">
                      <label>Slogan de votre site :</label> 
                      <input class="inscription" name="slogan" type="text" value="<?php echo $slogan; ?>" size="40">
					  </div>
                      <br clear="all">
                      <div style="margin-bottom:2px">
                      <label>Url du site :</label> 
                      <input class="inscription" name="url_site" type="text" value="<?php echo $site_url; ?>" size="40">
					  </div>
                      <br clear="all">                     
                      <div style="margin-bottom:2px">
                      <label>Mail noreply:</label>              
                      <input class="inscription" name="noreply" type="text" value="<?php echo $noreply; ?>" size="40">
					  </div>
                      <br clear="all">
                      <div style="margin-bottom:10px">
                      <label>Adresse mail :</label>              
                      <input class="inscription" name="mail" type="text" value="<?php echo $mail_site; ?>" size="40">
					  </div>                                            
				</fieldset>
              <br />
              <input class="button2" type="submit" name="Valider"  value="Valider" />
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