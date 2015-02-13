<?php
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donnée

// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion') {
	if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['pass']) && !empty($_POST['pass']))) {
		connexion_bd();
		$login = mysql_real_escape_string($_POST['login']);
		$passe = mysql_real_escape_string(md5($_POST['pass']));
		// on teste si une entrée de la base contient ce couple login / pass
		$sql = mysql_query("SELECT * FROM membres WHERE membPseudo='$login' AND membPasse='$passe' AND membActivation ='1'");
		if(mysql_num_rows($sql) == 0) {
			$erreur = '<div id="erreur_connexion">Compte non reconnu</div>';
		}	
	    else{       
            while($result = mysql_fetch_array($sql)){
                //si le compte na pas été validé
                if($result['membActivation']== '0'){
                    $erreur = '<div id="erreur_connexion" >Votre compte n\'pas activé</div>';
                }		
                //si le compte a été black-listé
                elseif($result['membActivation']== '2'){
                    $erreur = '<div id="erreur_connexion" >Vous avez été banni</div>';
                }	
				else {
				session_start();
				$_SESSION['login'] = $_POST['login'];
				$_SESSION['mdp'] = md5($_POST['pass']);
				$_SESSION['membID'] = $result['membID'];
				$_SESSION['membActivation'] = $result['membActivation'];
			
			//partie pour la connexion auto	
			
			if (isset($_POST['rester'])){
				$check=$_POST['rester'];
				connexion_bd();
				$login = mysql_real_escape_string($_POST['login']);
				$passe = mysql_real_escape_string(md5($_POST['pass']));
				$sql = mysql_query("SELECT * FROM membres WHERE membPseudo='$login' AND membPasse='$passe' AND membActivation ='1'");
				$data = mysql_fetch_assoc($sql);
				$membID = $_SESSION['membID'];
				$date=date("Ymd");
				$heure=date("H:i");
				$query_update = "UPDATE `membres` SET `membDateConnection` = '$date',`membHeureConnection` = '$heure' WHERE `membID` = '$membID'";
				mysql_query($query_update) or die(mysql_error());
				$expiration = time()+30*24*3600;
				$hash_cookie = md5($data['membMail']);
				setcookie('connexion_automatique', $hash_cookie, $expiration, null, null, false, true); //creation du cookie
			}
				if($result['membPseudo'] == $pseudo_admin ){ 
					//si admin on redirige vers la page admin
					$connexion = '<div class="ok">Connexion réussit. Redirection en cours...<script type="text/javascript"> window.setTimeout("location=(\'admin_retro/retro_index.php\');",1000) </script>';			
				}	
				else {
					// sinon vers la page index.php
					$connexion = '<div class="ok">Connexion réussit. Redirection en cours...<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",1000) </script>';			
				}
			}
		}
	}
	mysql_free_result($sql);
}
}


if (isset($_GET['act']) && $_GET['act']== 'recovery'){
if(isset($_POST['mdp'])){
    //si l'email vide
    if(empty($_POST['mail'])){
        $echec = '<div style="color:red; font-weight:bold;display: inline-block;">Veuillez saisir un email!</div>';
    }
    //si l'email est invalide
    else if (!preg_match("$[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$",$_POST['mail'])){
        $echec = '<div style="color:green; font-weight:bold">Veuillez saisir un email valide!</div>';
    }
    //c'est ok
    else{
		
        //On sélectionne les données
        $index = mysql_query("SELECT * FROM membres WHERE membMail='".mysql_real_escape_string(stripcslashes($_POST['mail']))."'");
        //si pas de résultat
        if(mysql_num_rows($index) == 0)
        {
            $echec = '<div style="color:red; font-weight:bold">Cette adresse mail n\'existe pas</div>';
        }
        //si c'est ok
        else{	
        //on boucle pour récupérer le pseudo et pass du membre pour lui envoyer
            while($result = mysql_fetch_array($index)){
				 $mailheaders = 'From: '.$noreply."\n";
				 $mailheaders .= "MIME-version: 1.0\n";
				 $mailheaders .= "Content-type: text/html; charset= utf-8; boundary=\"$boundary\"\n";
				 
				 $destinataire = $_POST['mail']."\n";
				 $subject = "Renouvellement de votre mot de passe";
				 $msg = "Bonjour,<br />";
				 $msg .= "vous avez demandé le renouvellement de votre mot de passe.<br />";
				 $msg .= "Pour effectuer cette modification veuillez cliquer sur le lien ci-dessous et suivez les instructions<br /><br />";
				 $msg .= "<a href='".$site_url."/connexion.php?act=change&id=".$result['membID']."&key=".$result['membPasse']."'>Renouvellez votre mot de passe</a>";
				 $msg .= "Pour information votre login est : ".$result['membPseudo']."";
				 $msg .= "L\'équipe ".$nom_site; 
				//on envoie l'email
                mail($destinataire, $subject, $msg, $mailheaders);
                //on laisse un message de confirmation                
                $echec = '<div style="color:green; font-weight:bold">Une erreur vient de survenir lors de l\'envoie du mail</div>
				<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';                
            }
        }    
    }
}
}
if (isset($_GET['act']) && $_GET['act']== 'change'){
	
	if(isset($_POST['new_mdp'])){	
        if ($_POST['pass'] != $_POST['pass_confirm']) { 
         $echec ='<div class="erreur"><a name="ok" style="text-decoration:none;color:white">Les 2 mots de passe sont différents.</a></div>'; 
        } 
        else if (empty($_POST['pass']) || strlen($_POST['pass']) < 6 || $_POST['pass']=="") {
         $echec = '<div class="erreur"><a name="ok" style="text-decoration:none;color:white">Le mot de passe doit contenir 6 caractères minimum.</a></div>';
        }
		else {
			$id_c=$_GET['id'];
			$key_c=$_GET['key'];
			$modif = mysql_query('UPDATE membres SET mdp="'.mysql_real_escape_string(sha1($_POST['pass'])).'" WHERE id="'.$id_c.'" AND mdp="'.$key_c.'"');     
									  
				if (!$modif) {
				 die('Requête invalide : ' . mysql_error());
				}
				else{
					$echec ='<div class="ok">Mise à jour éffectué avec succès. Redirection en cours...</div><script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';		
					} 					
		}
}
}

//ob_end_flush();	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo $nom_site.' - '.$slogan?></title>
    <META NAME="Description" CONTENT="<?php echo $description ?>">
        <META NAME="Keywords" CONTENT="<?php echo $keywords ?>">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta name="robots" content="<?php echo $robots ?>">
        <meta name="author" content="<?php echo $pseudo_admin ?>" />
        <meta name="expires" content="NEVER" />
        <meta name="copyright" content="<?php echo $nom_site ?>.com" />
        <meta name="audience" content="Tous" />
        <meta name="rating" content="general" />           
		<!-- Default CSS -->
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen, print, handheld" />
		<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" />	
		<link rel="icon" type="image/png" href="./img/icon.png" />
				<?php include('js/javascript.js'); ?>
		<?php include('js/css_javascript.js'); ?>
			<?php include_once("js/analyticstracking.php") ?>
	</head>
	<body>
		<div id="global">
		
        	<?php include('inc/header.php'); ?>
			<?php include('inc/menu.php'); ?> 
			<?php include('inc/bibli.php'); ?>  
			<?php include('inc/visites_site.php'); ?>  			
            <div id="main">
            	<div id="main_connexion">
                <?php		
                if (isset($_GET['act']) && $_GET['act'] =='recovery'){
                ?> 
                <h1>Entrez votre adresse mail</h1> 
                <?php
                } else if (isset($_GET['act']) && $_GET['act'] =='change'){
                ?> 
                <h1>Nouveau mot de passe</h1> 
				<?php
                } else { ?>
                    <h1>Connexion</h1>     
                <?php } ?>    
                                                          
                <div id = "connexion">
                <?php
                if (isset($_GET['act']) && $_GET['act'] =='recovery'){
                ?> 
                <form action="#" method="post">   
                
                    <p><label for = "mail" style="width:125px">Votre adresse mail: </label><input class="inscription" type="text" name="mail" value="" style="float:left;margin-bottom:2px;width:200px"></p>
                    <p><input class="boutton" name="mdp" value="Envoyer" type="submit"/></p><br />
                    <div class="reco"><a href="#null" onclick="javascript:history.back();">Retour</a></div><br />
                	<?php if (isset($echec)) echo $echec ?>
                </form>  
                <br  />
                <?php
                } else if (isset($_GET['act']) && $_GET['act'] =='change'){
                ?> 
                <form action="#" method="post">   
                  <p><label for = "pass" style="width:125px">Mot de passe : </label><input class="inscription" type="password" name="pass" value="" style="float:left;margin-bottom:2px;width:200px"></p>
                  <p><label for = "pass_confirm" style="width:125px">Confirmez mot de passe : </label><input class="inscription" type="password" name="pass_confirm" value="" style="float:left;margin-bottom:2px;width:200px"></p>
                  <p><input class="boutton" name="new_mdp" value="Envoyer" type="submit"/></p><br />
                  <div class="reco"><a href="#null" onclick="javascript:history.back();">Retour</a></div><br />
                  <?php if (isset($echec)) echo $echec ?>
                </form>  
                <br  />
                <?php
                } else { ?>
                    <form action="connexion.php" method="post">
                    <p><label for = "pseudo" style="width:125px">Login : </label><input class="inscription" type="text" name="login" value="<?php if (isset($_POST['login'])) echo htmlentities(trim($_POST['login'])); ?>" style="float:left;margin-bottom:2px;width:200px"></p>
                    <p><label for = "pass" style="width:125px">Mot de passe :</label> <input class="inscription" id="pass" type="password" name="pass" value="<?php if (isset($_POST['pass'])) echo htmlentities(trim($_POST['pass'])); ?>" style="float:left;margin-bottom:2px;width:200px"></p>
                    <p><input class="boutton" type="submit" name="connexion" value="Connexion" /></p><br />
                    <div class="reco" style="width: 337px;">Rester connecté <input type="checkbox" checked="checked" name="rester" style="vertical-align:middle;" /></div><br />
                    <div class="reco"><a href="connexion.php?act=recovery">Mot de passe oublié</a></div><br />
                    <?php 
                        if (isset($connexion)) echo '<b>'.$connexion.'</b>';
                        else if (isset($erreur)) echo '<b>'.$erreur.'</b>';
						else if (isset($erreur_a)) echo '<b>'.$erreur_a.'</b>';
 
                    ?>

                    </form>               
                    <?php
                    }
                    ?>
                </div>
              </div>
            </div>
            <?php 
			include('inc/footer.php');
			mysql_close();
			?>
        </div>
	</body>
</html>