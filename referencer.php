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
				echo "<div id='blcConteneur'>";
if((!empty($_SESSION["membID"])) && ($_SESSION["membActivation"] ==1))
{
$query_select=" SELECT * FROM consoles ORDER BY consNom ASC";
$result_select = mysql_query($query_select) or die(mysql_error());
echo "<h1>
		Ajouter un jeu
		</h1>
		<p>
		En tant que membre vous avez la possibilité d'ajouter un jeu qui n'est encore référencer dans la ludothéque.<br>
		Cela vous permet de compléter la ludothéque et ainsi en faire profiter toute la communauté.
		</p>
		";
		?>
		<form enctype='multipart/form-data' method='POST' action='#'>
			<div class="inscri">
			<label>Titre du jeu *:</label>
			<input type='text' value="<?php if (!empty($_POST["jeuNom"])) { echo stripcslashes(htmlspecialchars($_POST["jeuNom"],ENT_QUOTES)); } ?>" name='jeuNom' maxlength='45' size='45' class="inscription">
			</div>
			<div class="inscri">
			<label>Console *:</label>
			<select name='consoleID' class="inscription">
					<option value=''></option>
		<?php
	while($row_select = mysql_fetch_assoc($result_select)){
		echo "<option value='".$row_select["consID"]."' ";
		//echo ">".$row_select["consID"]."</option>";
			if (isset($_POST["consoleID"])){
				if($_POST["consoleID"] == $row_select["consID"])
				{
				echo " selected";
				}
			}
		echo ">".$row_select["consNom"]."</option>";
	}
	?>	
			</select> 
			</div>
			<div class="inscri">
			<label>Editeur *: </label>
			<input type='text' value="<?php if (isset($_POST["jeuEditeur"])) { echo stripcslashes(htmlspecialchars($_POST["jeuEditeur"],ENT_QUOTES)); } ?>" name='jeuEditeur' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Développeur *:</label>
			<input type='text' value="<?php if (isset($_POST["jeuDeveloppeur"])) { echo stripcslashes(htmlspecialchars($_POST["jeuDeveloppeur"],ENT_QUOTES)); } ?>" name='jeuDeveloppeur' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Genre *:</label>
			<input type='text' value="<?php if (isset($_POST["jeuGenre"])) { echo stripcslashes(htmlspecialchars($_POST["jeuGenre"],ENT_QUOTES)); } ?>" name='jeuGenre' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Version *:</label>
				<select name='jeuVersion' class='inscription'>
						<option value='1' <?php if (isset($_POST["jeuVersion"])){if($_POST["jeuVersion"]==1){echo " selected";}} ?> >Européenne</option>
						<option value='2' <?php if (isset($_POST["jeuVersion"])){if($_POST["jeuVersion"]==2){echo " selected";}} ?> >Japonaise</option>
						<option value='3' <?php if (isset($_POST["jeuVersion"])){if($_POST["jeuVersion"]==3){echo " selected";}} ?> >Amércaine</option>
					</select>
			</div>

			<div class="inscri">
			<label>Date de sortie :</label>
			<input type='text' value="<?php if (!empty($_POST["numJour"])) { echo stripcslashes(htmlspecialchars($_POST["numJour"],ENT_QUOTES)); } ?>" name='numJour' maxlength='2' size='2' class='inscription' style='width:50px;'> <input type='text' value="<?php if (!empty($_POST["numMois"])) { echo stripcslashes(htmlspecialchars($_POST["numMois"],ENT_QUOTES)); } ?>" name='numMois' maxlength='2' size='2' class='inscription' style='width:50px;'> <input type='text' value="<?php if (!empty($_POST["numAnnee"])) { echo stripcslashes(htmlspecialchars($_POST["numAnnee"],ENT_QUOTES)); } ?>" name='numAnnee' maxlength='4' size='2' class='inscription' style='width:50px;'>
			</div><br><center>
			<div class="inscri">
					<input type='hidden' name='MAX_FILE_SIZE' value='153200'>
					<input type='file' name='jeuImg' size='45' class="inscription">
			</div><br></center>
			<div class="inscri">
			<input type='submit' name='btnAjouter' value='Ajouter' class='boutton'>
			</div>
			<span style="color:#F00;font-weight:bold;">Remplissez les champs obligatoires</span>   
		</form>
		<?php		
		if(isset($_POST['btnAjouter']) && $_POST['btnAjouter']=='Ajouter'){
		//si titre vide
		if(empty($_POST['jeuNom'])){
			echo '<div class="erreur">Entrez un titre</div>';
		}
		//si console vide
		else if(empty($_POST['consoleID'])){
			echo '<div class="erreur">Choisissez une console</div>';
		}
		else if(empty($_POST['jeuEditeur'])){
			echo '<div class="erreur">Entrez un éditeur</div>';
		}
		else if(empty($_POST['jeuDeveloppeur'])){
			echo '<div class="erreur">Entrez un développeur</div>';
		}
		else if(empty($_POST['jeuGenre'])){
			echo '<div class="erreur">Entrez un genre</div>';
		}
		//c'est ok on traite l'image
		else{
			$Nom = $_FILES['jeuImg']['name'];
			$Exts = array('.jpg');
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
				$Exts = array('.jpg');
				$Ext = substr($Nom, strrpos($Nom,'.'));
				// Traitement relatif au télécharmgent du fichier
				$query_select = "SELECT MAX(jeuID) as nbMax FROM jeux";
				$result_select = mysql_query($query_select) or die(mysql_error());
				$row_select = mysql_fetch_assoc($result_select);
				$numero = (($row_select['nbMax']) + 1);
				$Dest = "./img/jeux/".$numero.$Ext;

				if(($_FILES['jeuImg']['error'] === 0)
				&&(is_uploaded_file($_FILES['jeuImg']['tmp_name']))
				&&(move_uploaded_file($_FILES['jeuImg']['tmp_name'],$Dest)))
				{
				$jeuNom = mysql_real_escape_string($_POST["jeuNom"]);
				$consID = mysql_real_escape_string($_POST["consoleID"]);
				$jeuDeveloppeur = mysql_real_escape_string($_POST["jeuDeveloppeur"]);
				$jeuEditeur = mysql_real_escape_string($_POST["jeuEditeur"]);
				$jeuDateSortie = mysql_real_escape_string($_POST["numAnnee"]).mysql_real_escape_string($_POST["numMois"]).mysql_real_escape_string($_POST["numJour"]);
				$jeuGenre = mysql_real_escape_string($_POST["jeuGenre"]);
				$jeuVersion = mysql_real_escape_string($_POST["jeuVersion"]);
				$jeuAjouterPar = $_SESSION["membID"]; 
				$MAJ_Ext = substr($Ext,1);
				$query_insert="INSERT INTO jeux 
							  (jeuID,jeuIDCons,jeuNom,jeuEditeur,jeuDeveloppeur,jeuDateSortie,jeuVersion,jeuExtImg,jeuAjouterPar,jeuGenre,jeuActivation)
						VALUES('$numero','$consID','$jeuNom','$jeuEditeur','$jeuDeveloppeur','$jeuDateSortie','$jeuVersion','$MAJ_Ext','$jeuAjouterPar','$jeuGenre','0')
									";
				mysql_query($query_insert) or die(mysql_error());
				mysql_close();
				echo '<div class="ok"><center>Le jeu à été créé avec succé et doit maintenant être validé, vous allez être redirigé...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'referencer.php\');",3000) </script>';
				}
				else
				{
				echo "<div class='erreur'>Erreur lors du téléchargement. Veuillez rétrécir l'image</div>";
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
		<br> Vous pourrez ensuite référencer des jeux pour les ajouter à votre collection mais également en faire profiter toute la communauté.
	</div>
	";
	
}
				?>			   
			</div>  
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>