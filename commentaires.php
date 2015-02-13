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
if(isset($_GET['x'])) {
				$arID = fp_getURL($_GET["x"]); // récupération de l'id de l'article
				connexion_bd(); //connexion a la base de donnée
				$sql = "SELECT * FROM articles,membres WHERE articles.arID = $arID";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 10 news
                $row = mysql_fetch_assoc($result);
                
				 $date = substr($row['arDate'],6,2)."/".substr($row['arDate'],4,2)."/".substr($row['arDate'],0,4);
                ?>	
                <div id="stat">
                <div style="margin-top:20px;">
                    <h1>Les news</h1>
                </div>    				
                <div id="news">
                    <div class="titre">
                    	<h2><?php echo $row['arTitre'] ?></h2>
                    </div>
                    <div class="corp">
                    	<?php echo $row['arTexte'] ?>
                    </div>
                    <div class="pied">
                        Par <?php echo $row['membPseudo'].' le '.$date?>
                    </div>
                </div>
				
				
				<div style="margin-top:20px;">
                	<h1>Commentaires</h1>
                </div>
				<?php 
				$sql = "SELECT * FROM commentaires,membres WHERE commentaires.comAjouterPar = membres.membID AND comIDArticle = '$arID' ORDER BY comID DESC";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 2 news
                while($row = mysql_fetch_assoc($result))
                 {
				 $date = substr($row['comDate'],6,2)."/".substr($row['comDate'],4,2)."/".substr($row['comDate'],0,4);
                ?>				
                <div id="news">
                    <div class="titre">
                    </div>
                    <div class="corp">
                    	<?php echo $row['comTexte'] ?>
                    </div>
                    <div class="pied">
                        Par <?php echo $row['membPseudo']." le ".$date." à ".$row["comHeure"]; ?>
                    </div>
                </div>
                <?php } ?>
				
				
				
				
				
                <?php } 
else {  
				connexion_bd(); //connexion a la base de donnée
				$sql = "SELECT * FROM articles,membres WHERE articles.arAjouterPar = membres.membID ORDER BY arID DESC";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 10 news
				echo "<div id='stat'>
                <div style='margin-top:20px;'>
                    <h1>Les news</h1>
                </div>";    	
                while($row = mysql_fetch_assoc($result))
                 {
				 $date = substr($row['arDate'],6,2)."/".substr($row['arDate'],4,2)."/".substr($row['arDate'],0,4);
                ?>						
                <div id="news">
                    <div class="titre">
                    	<h2><?php echo $row['arTitre'] ?></h2>
                    </div>
                    <div class="corp">
                    	<?php echo $row['arTexte'] ?>
                    </div>
                    <div class="pied">
                        Par <?php echo $row['membPseudo'].' le '.$date?>
                    </div>
                </div>
                <?php } 
	 } ?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>