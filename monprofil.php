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
if((!empty($_SESSION["membID"])) && ($_SESSION["membActivation"] ==1))
{
if((!isset($_POST["btnProfilImage"])) && (!isset($_POST["btnCollecImage"])))
{
	echo "<h1>Mon profil</h1>";
	$membID = $_SESSION["membID"];
	$query_select = "SELECT membres.*, count(DISTINCT(appartient.appartIDCons)) as nbConsoles, count(DISTINCT(comporte.compIDJeu)) as nbJeux
					FROM membres,collections,appartient,comporte,visites_collection
					WHERE membres.membID = collections.collIDMemb
					AND collections.collID = appartient.appartIDColl
					AND collections.collID = comporte.compIDColl

					AND appartient.appartIDColl = '$membID'
					AND comporte.compIDColl = '$membID'
					AND membres.membID = '$membID'
					AND visites_collection.visitIDColl  = '$membID'";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$row_select = mysql_fetch_assoc($result_select);
	$repertoire_image = "./img/membres/";
	$image = $repertoire_image.$membID.".".$row_select["membImg"];
	$membPseudo = $row_select["membPseudo"];
	$membMail = $row_select["membMail"];
	$nbConsoles = $row_select["nbConsoles"];
	$nbJeux = $row_select["nbJeux"];
	$nbVisites = $row_select["nbVisites"];
	$dateInscription = substr($row_select['membDateInscription'],6,2)."/".substr($row_select['membDateInscription'],4,2)."/".substr($row_select['membDateInscription'],0,4);
	$DateConnection = substr($row_select["membDateConnection"],6,2)."/".substr($row_select["membDateConnection"],4,2)."/".substr($row_select["membDateConnection"],0,4);
	echo "<div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'><table>
			<tr>
				<td width='200px'>
					<img src='$image' width='100px' height='91px' />
				</td>
				<td width='200px'>
					Pseudo :<br>
					Date d'inscription :<br>
					Nombres de consoles :<br>
					Nombres de jeux :<br>
					Derniére connection :
				</td>
				<td width='200px'>
					<b>$membPseudo</b><br>
					<b>$dateInscription</b><br>
					<b>$nbConsoles</b><br>
					<b>$nbJeux</b><br>		
					<b>$DateConnection</b><br>
				</td>
			</tr>
			<tr>
				<td>		
					<form enctype='multipart/form-data' method='POST' action='monprofil.php'>
					Changer ma photo :
				</td>
				<td colspan='2'>
					<input type='hidden' name='MAX_FILE_SIZE' value='256000'>
					<input type='file' name='photo' size='45'>		
					<input type='submit' name='btnProfilImage' value='Telecharger'>
				</td>
			</tr>
			</form>
		</table>
		</div>
		<div class='pied'>
          </div>
         </div>
		<h1>
				Photos de ma collection
		</h1>
		<div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'>
				<table>";
	# Ici on télécharge les photos de la collections du membre
	$query_select = "SELECT * FROM photos_collection WHERE phIDColl = '$membID'";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$repertoire_image = "./img/membres/collections/";
	$i = 1;
	while($row_select = mysql_fetch_assoc($result_select)) {
		$image = $repertoire_image.$membID."_".$row_select["phNumero"].".".$row_select["phExt"];
		if($i == 1)
		{ echo "<tr>";}
		echo "<td width='100px'>
				<a class='fancybox' href='$image' data-fancybox-group='gallery' title=''><img src='$image' alt='' width='100px' height='80px'/></a>
			</td>";
		
		$i++;
		if($i > 5)
		{ echo "</tr>"; $i=1;} 
	}
	# fermeture du tableau
	if($i <> 1)
	{ 
	echo "<td colspan='$i' width='100px'></td></tr>";
	}
			
		echo"<tr>
				<form enctype='multipart/form-data' method='POST' action='monprofil.php'>

				<td width='150px'>
					Ajouter une photo :
				</td>
				<td colspan='3' width='150px' >
					<input type='hidden' name='MAX_FILE_SIZE' value='256000'>
					<input type='file' name='photo' size='45'>
					<input type='submit' name='btnCollecImage' value='Telecharger'> 
				</td>
			</tr>
			</form>
		</table>
		</div>
		<div class='pied'>
          </div>
         </div>
		 <div style='margin-top:20px;'>
                	<h1>Jeux favoris</h1>
                </div>
				<div id='news'>";
			
				$rep_img = './img/jeux/';
			
                $sql = "SELECT * FROM comporte,jeux,consoles WHERE comporte.compIDJeu = jeux.jeuID AND comporte.compIDCons = consoles.consID AND compIDColl = '$membID' AND favori = '1' ORDER BY compIDCons ASC";
 
				$result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 2 news
                while($row = mysql_fetch_assoc($result))
                 {
				$codeAffichage = $row["codeAffichage"];
				$jeuID = $row["jeuID"];
				$image = $rep_img.$row["jeuID"].'.'.$row["jeuExtImg"];
				$consID = $row["jeuIDCons"];
				# Amelioration de l'affichage
				if($codeAffichage == 1){
				#Affichage type Megadrive
				$type="width='75px' height='93px'";}
				elseif($codeAffichage == 2)
				{$type="width='79px' height='108px'";}
				elseif($codeAffichage == 3){
				#Affichage type Nintendo
				$type="width='105px' height='75px'";}
				elseif($codeAffichage == 4){
				#Affichage type PSX
				$type="width='90px' height='90px'";}
				else{
				#Affichage type PSX
				$type="width='90px' height='90px'";}
				echo "<img src='$image' $type/> &nbsp; &nbsp;";
                 }
	echo "		
				</div>
	";
}
elseif(isset($_POST["btnProfilImage"]))
{

	$membID = $_SESSION["membID"];
	$Exts = array('.jpg','.gif','.png','.JPG');
	$Nom = $_FILES['photo']['name'];
	$Ext = substr($Nom, strrpos($Nom,'.'));
	# Vérification de la taille du fichier
	if($_FILES['photo']['size'] > 256000)
	{
		$_SESSION['message']="Taille du fichier trop grande !!";
	}
	# Vérification de l'extension du fichier
	elseif(!in_array($Ext,$Exts))
	{
		$_SESSION['message']="Extension non valide !!";
	}
	else
	{	

	$Dest = "./img/membres/".$membID.$Ext;
		if (($_FILES['photo']['error'] === 0)
		&& (is_uploaded_file($_FILES['photo']['tmp_name']))
		&& (move_uploaded_file($_FILES['photo']['tmp_name'],$Dest)))
		{
	
			#update de la table blogs
			$MAJ_Ext = substr($Ext,1);
			$query_update="UPDATE membres SET membImg = '$MAJ_Ext' WHERE membID = '$membID'";
			mysql_query($query_update) or die(mysql_error());
			mysql_close();
			echo '<div class="ok"><center>La photo de votre profil a été téléchargé avec succés. Redirection en cours...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'monprofil.php\');",3000) </script>';
		}
		else
		{
			echo "<div class='erreur'>Erreur lors du téléchargement. Veuillez rétrécir l'image</div>";
		}
	
	}
}
elseif(isset($_POST["btnCollecImage"]))
{	
	$membID = $_SESSION["membID"];
	$Exts = array('.jpg','.gif','.png','.JPG');
	$Nom = $_FILES['photo']['name'];
	$Ext = substr($Nom, strrpos($Nom,'.'));
	# Vérification de la taille du fichier
	if($_FILES['photo']['size'] > 256000)
	{
		$_SESSION['message']="Taille du fichier trop grande !!";
	}
	# Vérification de l'extension du fichier
	elseif(!in_array($Ext,$Exts))
	{
		$_SESSION['message']="Extension non valide !!";
	}
	else
	{	
	# On compte combien il y a de photos dans cette collection
	$query_select = "SELECT count(*) as nbPhotos FROM photos_collection WHERE phIDColl = '$membID'";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$row_select = mysql_fetch_assoc($result_select);
	$numero = (($row_select['nbPhotos']) + 1);
	# Ensuite on la télécharge
	$Dest = "./img/membres/collections/".$membID."_".$numero.$Ext;
		if (($_FILES['photo']['error'] === 0)
		&& (is_uploaded_file($_FILES['photo']['tmp_name']))
		&& (move_uploaded_file($_FILES['photo']['tmp_name'],$Dest)))
		{

			#update de la table blogs
			$MAJ_Ext = substr($Ext,1);
			$query_update="INSERT INTO photos_collection
					      (phID,phIDColl,phNUmero,phExt)
					VALUES('','$membID','$numero','$MAJ_Ext')
							";
			mysql_query($query_update) or die(mysql_error());
			mysql_close();
			echo '<div class="ok"><center>La photo de votre collection a été téléchargé avec succés. Redirection en cours...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'monprofil.php\');",3000) </script>';
		}
		else
		{
			echo "<div class='erreur'>Erreur lors du téléchargement. Veuillez rétrécir l'image</div>";
		}
	
	}
}
}

else
{
echo "<h1>Zone reservée</h1>
		<div class='erreur'> 
		Cette zone est réservée aux membres !!<br>
		 <a href='http://retrogamerstock.fr/php/inscription.php'>Inscrivez-vous</a> GRATUITEMENT ou <a href='http://www.retrogamerstock.fr/php/login.php'>identifiez-vous</a>
		<br> Vous pourrez ensuite gérer votre collection en ligne.
	</div>
	";
}
?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>