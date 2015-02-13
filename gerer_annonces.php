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
	echo "<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>Mes annonces</h1>
				</div>";
				
	$query_select="	SELECT * FROM annonces WHERE anAjouterPar = $membID AND anActivation = 1";
	$result_select = mysql_query($query_select) or die(mysql_error());	
	# AFFICHAGE DE LA SELECTION
	#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE
	$rep_img = './img/membres/annonces/';
	$i = 0;
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
											<form method = 'POST' action='desactiver_annonce.php'>
												<input type='submit' name='btnAction' value='Supprimer' class='collec'>
												<input type='hidden' name='anID' value='$anID'>
											</form>
								</td>
								<td width='200px'>
									<a class='fancybox' href='$image' title=''><img src='$image' alt='' width='100px' height='80px'/></a>
								</td>
								<td width='400px'>
									$anTexte
								</td>
							</tr>						
						</table>
                    </div>
                    <div class='pied'>

                     Créée le $anDate à $heure
                    </div>
                </div>";

	}
	echo "</div>";

}
else
{
echo "<h1>Zone reservée</h1>
		<div class='erreur'> 
		Cette zone est réservée aux membres !!<br>
		 <a href='http://retrogamerstock.fr/inscription.php'>Inscrivez-vous</a> GRATUITEMENT ou <a href='http://www.retrogamerstock.fr/connexion.php'>identifiez-vous</a>
		<br> Vous pourrez ensuite publier vos annonces.
	</div>
	";
}
?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>