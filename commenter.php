<?php
session_start(); //ont demarre la session
//Script par Zelix pour Retrogamerstock
require "php/parametres.php";
connexion_bd(); //connexion a la base de donnée
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Retrogamerstock</title>
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
            <?php

if((!empty($_SESSION["membID"])) && ($_SESSION["membActivation"] == 1) &&(isset($_GET["x"])))
{
$arID = fp_getURL($_GET["x"]); // récupération de l'id de l'article
$membID = $_SESSION["membID"];
echo "<h1>
		Ajouter un commenaitre
		</h1>
		<p>
		En tant que membre vous avez la possibilité de commenter les articles.<br>
		</p>
		";
		?>
		<form enctype='multipart/form-data' method='POST' action='#'>
			<div class="inscri">
			<label>Commentaire *:</label>
			<textarea rows="3" cols="30" name="commentaire" class="inscription"> 
			<?php if (!empty($_POST["commentaire"])) { echo stripcslashes(htmlspecialchars($_POST["commentaire"],ENT_QUOTES)); } ?>
			</textarea>
			</div><br>
			<div class="inscri">
			<input type='submit' name='btnAjouter' value='Ajouter' class='boutton'>
			</div>
			<span style="color:#F00;font-weight:bold;">Remplissez les champs obligatoires</span>   
		</form>
		<?php		
		if(isset($_POST['btnAjouter']) && $_POST['btnAjouter']=='Ajouter'){
		//si titre vide
		$textarea = stripcslashes(htmlspecialchars($_POST["commentaire"],ENT_QUOTES));
		if(empty($textarea)){
			echo '<div class="erreur">Entrez un commentaire</div>';
		}
		//c'est ok on ajoute le commentaire
		else{
				$comTexte = stripcslashes(htmlspecialchars($_POST["commentaire"],ENT_QUOTES));
				$date=date("Ymd");
				$heure=date("H:i");
				$query_insert="INSERT INTO commentaires 
							  (comID,comTexte,comDate,comHeure,comAjouterPar,comActivation,comIDArticle)
						VALUES('','$comTexte','$date','$heure','$membID','1','$arID')
									";
				mysql_query($query_insert) or die(mysql_error());
				mysql_close();
				echo '<div class="ok"><center>Le commentaire à été ajouté avec succé, vous allez être redirigé...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';

		}
}
}
else
{
echo "<h1>Zone reservée</h1>
		<div class='erreur'>
		Cette zone est réservée aux membres !!<br>
		 <a href='http://retrogamerstock.fr/inscription.php'>Inscrivez-vous</a> GRATUITEMENT ou <a href='http://www.retrogamerstock.fr/connexion.php'>identifiez-vous</a>
		<br> Vous pourrez ensuite commenter les articles du site.
	</div>
	";
	
}
				?>			   
			</div>  
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>