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

if((!empty($_SESSION["membID"])) && ($_SESSION["membActivation"] == 1))
{
$membID = $_SESSION["membID"];
echo "<h1>
		Ajouter une annonce
		</h1>
		";
		?>
		<form enctype='multipart/form-data' method='POST' action='ajouter_annonce.php'>
			<div class="inscri">
			<label>Titre *: </label>
			<input type='text' value="<?php if (isset($_POST["anTitre"])) { echo stripcslashes(htmlspecialchars($_POST["anTitre"],ENT_QUOTES)); } ?>" name='anTitre' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Description *:</label>
			<textarea rows="3" cols="30" name="description" class="inscription"> 
			<?php if (!empty($_POST["description"])) { echo stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES)); } ?>
			</textarea>
			</div><br>
			<div class="inscri">
					<input type='hidden' name='MAX_FILE_SIZE' value='153200'>
					<input type='file' name='jeuImg' size='45' class="inscription">
			</div><br></center>
			<div class="inscri">
			<div class="inscri">
			<input type='submit' name='btnAjouter' value='Ajouter' class='boutton'>
			</div>
			<span style="color:#F00;font-weight:bold;">Remplissez les champs obligatoires</span>   
		</form>
		<?php		
		if(isset($_POST['btnAjouter']) && $_POST['btnAjouter']=='Ajouter'){
		//si titre vide
		$textarea = stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES));
		if(empty($textarea)){
			echo '<div class="erreur">Entrez un description</div>';
		}
		//si console vide
		else if(empty($_POST['anTitre'])){
			echo '<div class="erreur">Entrez un titre</div>';
		}
		//c'est ok on traite l'image
		else{
			$Nom = $_FILES['jeuImg']['name'];
			$Exts = array('.jpg','.JPG');
			$Ext = substr($Nom, strrpos($Nom,'.'));
			if($_FILES['jeuImg']['size'] > 153200)
			{
					echo "<div class='erreur'>Taile de l'image trop grande</div>";
			}			
			# Vérification de l'extension du fichier
			elseif(!in_array($Ext,$Exts)) 
			{
				echo "<div class='erreur'>Format de l'image invalide</div>";
			}
			else{
			$Nom = $_FILES['jeuImg']['name'];
			$Exts = array('.jpg','.JPG');
			$Ext = substr($Nom, strrpos($Nom,'.'));
			if($_FILES['jeuImg']['size'] > 153200)
			{
					echo "<div class='erreur'>Taile de l'image trop grande</div>";
			}			
			# Vérification de l'extension du fichier
			elseif(!in_array($Ext,$Exts)) 
			{
				echo "<div class='erreur'>Format de l'image invalide</div>";
			}
			else
				{
				$Nom = $_FILES['jeuImg']['name'];
				$Exts = array('.jpg','.JPG');
				$Ext = substr($Nom, strrpos($Nom,'.'));
				// Traitement relatif au télécharmgent du fichier
				$query_select = "SELECT MAX(anID) as anMax FROM annonces";
				$result_select = mysql_query($query_select) or die(mysql_error());
				$row_select = mysql_fetch_assoc($result_select);
				$numero = (($row_select['anMax']) + 1);
				$Dest = "./img/membres/annonces/".$numero.$Ext;
				if(($_FILES['jeuImg']['error'] === 0)
				&&(is_uploaded_file($_FILES['jeuImg']['tmp_name']))
				&&(move_uploaded_file($_FILES['jeuImg']['tmp_name'],$Dest)))
				{
				echo "ok3";
				$anTexte = stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES));
				$anTitre = mysql_real_escape_string($_POST["anTitre"]);
				$MAJ_Ext = substr($Ext,1);
				$anAjouterPar = $_SESSION["membID"]; 
				$date=date("Ymd");
				$heure=date("H:i");
				$query_insert="INSERT INTO annonces 
							  (anID,anTitre,anTexte,anDate,anHeure,anAjouterPar,anImg,anActivation)
						VALUES('','$anTitre','$anTexte','$date','$heure','$anAjouterPar','$MAJ_Ext','0')
									";
				mysql_query($query_insert) or die(mysql_error());
				mysql_close();
				echo '<div class="ok"><center>Votre annonce est créé, elle sera valider trés prochainement...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';
				}
				else
				{
				echo "<div class='erreur'>Erreur lors du téléchargement. Veuillez rétrécir l'image</div>";
				}
			}
			}
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