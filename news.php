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
				include('inc/visites_articles.php');
				connexion_bd(); //connexion a la base de donnée
				$sql = "SELECT * FROM articles,membres WHERE articles.arID = $arID";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 10 news
                $row = mysql_fetch_assoc($result);
				//NB COMMENTAIRES
				$sql_count_comm = "SELECT count(comID) as nbCom FROM commentaires WHERE comIDArticle = '$arID' AND comActivation = '1'";
				$result_count_comm = mysql_query($sql_count_comm);
				$row_count_comm = mysql_fetch_assoc($result_count_comm);
				//NB VUES
				$sql_count_vues = "SELECT count(vueID) as nbVues FROM vues_article WHERE vueIDAr = '$arID'";
				$result_count_vues = mysql_query($sql_count_vues);
				$row_count_vues = mysql_fetch_assoc($result_count_vues);
                
				 $date = substr($row['arDate'],6,2)."/".substr($row['arDate'],4,2)."/".substr($row['arDate'],0,4);
                ?>	
                <div id="stat">
                <div style="margin-top:20px;">
                    <h1>Lire l'article</h1>
                </div>    				
                <div id="news">
                    <div class="titre">
                    	<h2><?php echo $row['arTitre'] ?></h2>
                    </div>
                    <div class="corp">
                    	<?php echo $row['arTexte'] ?>
                    </div>
                    <div class="pied">
					    <a href="<?php echo fp_makeURL('commenter.php', $row["arID"]); ?>">Commenter</a> |
						<?php
							// NB COMMENTAIRES
							if($row_count_comm["nbCom"] == 0)
							{
							echo "0 commentaire | ";
							}
							else
							{
							echo "<a href='".fp_makeURL('commentaires.php', $row["arID"])."'>Lire ".$row_count_comm["nbCom"]." commentaires</a> | ";
							}
							// NB VUES
							if($row_count_vues["nbVues"] == 0)
							{
							echo "0 vue | ";
							}
							else
							{
							echo $row_count_vues["nbVues"]." vues</a><br/> ";
							}
							?>
                        Par <?php echo $row['membPseudo'].' le '.$date?>
                    </div>
                </div>
                <?php } 
else {  
				connexion_bd(); //connexion a la base de donnée
				$sql = "SELECT * FROM articles,membres WHERE articles.arAjouterPar = membres.membID AND arType=2 ORDER BY arID DESC";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 10 news
				echo "<div id='stat'>
                <div style='margin-top:20px;'>
                    <h1>Les articles</h1>
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
                    	<?php echo $row['arResume'] ?>
						<a href="<?php echo fp_makeURL('news.php', $row["arID"]); ?>"><br/>[...]</a>
                    </div>
                    <div class="pied">
						<?php
						$arID = $row['arID'];
						$sql_count = "SELECT count(comID) as nbCom FROM commentaires WHERE comIDArticle = '$arID' AND comActivation = '1'";
						$result_count = mysql_query($sql_count);
						$row_count = mysql_fetch_assoc($result_count);
						//NB VUES						
						$sql_count_vues = "SELECT count(vueID) as nbVues FROM vues_article WHERE vueIDAr = '$arID'";
						$result_count_vues = mysql_query($sql_count_vues);
						$row_count_vues = mysql_fetch_assoc($result_count_vues)
						?>
                    	<a href="<?php echo fp_makeURL('commenter.php', $row["arID"]); ?>">Commenter</a> |
						<?php
							if($row_count["nbCom"] == 0)
							{
							echo "0 commentaire | ";
							}
							else
							{
							echo "<a href='".fp_makeURL('commentaires.php', $row["arID"])."'>Lire ".$row_count["nbCom"]." commentaires</a> | ";
							}
							// NB VUES
							if($row_count_vues["nbVues"] == 0)
							{
							echo "0 vue | ";
							}
							else
							{
							echo $row_count_vues["nbVues"]." vues</a> |";
							}
						?>
						<a href="<?php echo fp_makeURL('news.php', $row["arID"]); ?>">Lire la suite</a><br />
                        Par <?php echo $row['membPseudo']." le ".$date." à ".$row["arHeure"]; ?>
                    </div>
                </div>
                <?php } 
	 } ?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>