<?php
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donnée
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Inscription - Retrogamerstock</title>
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
            <?php if (isset($erreur)) echo $erreur; ?>
<h1>
			Inscrivez-vous
		</h1>
		<p>
		Inscrivez-vous pour rejoindre la communauté.<br/>Pour cela c'est trés simple, il vous suffit de remplir le formulaire ci-dessous.<br/>
		Un mail de confirmation vous sera envoyé afin de valider votre inscription.
		</p><br/>
		
		<form method = "POST" action="#">
        			<div class="inscri">
					<label>Pseudo *:</label> 
					<input type="text" value="<?php if (!empty($_POST["membPseudo"])) { echo stripcslashes(htmlspecialchars($_POST["membPseudo"],ENT_QUOTES)); } ?>" name="membPseudo" size="25" class="inscription">
                    </div>
                    <div class="inscri">
					<label>E-mail *:</label> 
					<input type='text' value="<?php if (!empty($_POST["membMail"])) { echo stripcslashes(htmlspecialchars($_POST["membMail"],ENT_QUOTES)); } ?>" name="membMail" size="25" class="inscription">
					</div>
                    <div class="inscri">
                    <label>Mot de passe *:</label> 
                    <input type="password" value="<?php if (!empty($_POST["membPasse"])) { echo stripcslashes(htmlspecialchars($_POST["membPasse"],ENT_QUOTES)); } ?>" name="membPasse" size="25" class="inscription">
					</div>
                    <div class="inscri">
                    <label>Confirmation *:</label>
					<input type="password" value="<?php if (!empty($_POST["confirmation"])) { echo stripcslashes(htmlspecialchars($_POST["confirmation"],ENT_QUOTES)); } ?>" name="confirmation" size="25" class="inscription">
					</div>
                    <br />
                    <div class="inscri">
            		<input type="submit" name="btnInscrire" value="Valider" class="boutton" style="width:193px">
                    </div>
                    <br />
                    <span style="color:#F00;font-weight:bold;">Tout les champs sont obligatoire<br />
					Un mail de confirmation va vous être envoyé. </span>           
         </form>
            
<?php
if(isset($_POST['btnInscrire']) && $_POST['btnInscrire']=='Valider'){
    //si pseudo vide
    if(empty($_POST['membPseudo'])){
        echo '<div class="erreur">Entrez un pseudo</div>';
    }
    //si mot de passe vide
    else if ($_POST['membPasse'] != $_POST['confirmation']) { 
          echo '<div class="erreur">Les 2 mots de passe sont différent</div>'; 
    } 
    else if (empty($_POST['membPasse']) || strlen($_POST['membPasse']) < 6 || $_POST['membPasse']=="") {
         echo '<div class="erreur">Le mot de passe doit contenir au moin 6 caractéres</div>';
    }
    //si l'email vide
    else if(empty($_POST['membMail'])){
        echo '<div class="erreur">Entrez une adresse mail</div>';
    }
    //si l'email est invalide
    else if (!preg_match("$[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$",$_POST['membMail'])){
        echo '<div class="erreur">Adresse mail invalide</div>';
    }
    //c'est ok
    else{
        //On vérifie si le pseudo existe en bd
        $pseudo = mysql_query("SELECT membPseudo FROM membres WHERE membPseudo='".mysql_real_escape_string(stripcslashes($_POST['membPseudo']))."'") or die ('Erreur :'.mysql_error());
        if(mysql_num_rows($pseudo) != 0)
        {
            echo '<div class="erreur">Pseudo déjà utilisé</div>'; return false;
        }
        //on vérifie si le mail existe en bd
        $email = mysql_query("SELECT membMail FROM membres WHERE membMail='".mysql_real_escape_string(stripcslashes($_POST['membMail']))."'") or die ('Erreur :'.mysql_error());
        if(mysql_num_rows($email) != 0)
        {
            echo '<div class="erreur">Adresse mail déjà enregistré</div>'; return false;
        }
        //tout est ok		
		else{
		//date du jour
		$date=date("Ymd");
			// on enregistre les données
	
			$insert = mysql_query("INSERT INTO membres (membID,membMail,membPseudo,membPasse,membDateInscription,membDateConnection,membHeureConnection,membActivation,membType,membImg) VALUES ( '', '".mysql_real_escape_string(stripcslashes($_POST['membMail']))."', 
																	'".mysql_real_escape_string(stripcslashes(utf8_decode($_POST['membPseudo'])))."', 
																	'".mysql_real_escape_string(stripcslashes(utf8_decode(md5($_POST['membPasse']))))."', 
																	'".mysql_real_escape_string($date)."', 
																	'".mysql_real_escape_string('0')."',
																	'".mysql_real_escape_string('0')."',
																	'".mysql_real_escape_string('0')."',
																	'".mysql_real_escape_string('1')."',
																	'".mysql_real_escape_string('')."') ");
															
																
			//Si il y a une erreur
			if (!$insert) {
				die('Requête invalide : ' . mysql_error());
			}
			
			//pas d'erreur d'enregistrement, on envoie un mail de confirmation
			else {
			$ID = mysql_insert_id(); 
			$query_insert_collection =mysql_query ("INSERT INTO collections VALUES ('".$ID."','".$ID."')");
			$url = fp_makeURL('http://retrogamerstock.fr/validation_inscription.php', $ID);
				
	// Format mail HTML
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// En tete
	$headers .= 'From: contact@retrogamerstock.fr' . "\r\n";
	$headers .= 'Cc: contact@retrogamerstock.fr' . "\r\n";

	$to      = $_POST['membMail'];
	$subject = 'RetrogamerStock : inscription';
	
	$messages .= "<html>
					<head>
						<title></title>
					</head>
					<body>
					<p>
					<h2>Bienvenue sur RetroGamerStock.fr</h2>
					Cliquer sur le lien suivant afin de valider votre inscription : <font color='blue'><a href='$url'>ici</a></font><br/><br/>
					Vous pouvez dés à présent gérer votre collection de jeux vidéo en ligne et rejoindre la communauté de joueurs<br/><br/>
					Merci pour votre inscription et a bientot<br/><br/>
					RetroGamerStock
					</p>
					</body>
					</html>";
	mail($to, $subject, $messages,$headers);

                echo '<div class="ok"><center>Votre compte à été créé avec succé, vous allez être redirigé...</center></div>
				<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';
            }      
        }               
        close_bd();   
    }
}
?>            
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>