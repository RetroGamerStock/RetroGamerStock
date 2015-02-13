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
 <META NAME='Description' CONTENT='<?php echo $description ?>' />
        <META NAME="Keywords" CONTENT="<?php echo $keywords ?>" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta name="robots" content="<?php echo $robots ?>" />
        <meta name="author" content="<?php echo $pseudo_admin ?>" />
        <meta name="expires" content="NEVER" />
        <meta name="copyright" content="<?php echo $nom_site ?>.com" />
        <meta name="audience" content="Tous" />
        <meta name="rating" content="general" />   
<meta name="alias" content="http://www.retrogamerstock.fr/" />
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
                  <div style="margin-top:20px;">
                	<h1>Bienvenue sur RetroGamerStock</h1>
                </div>
				 <div id="news">
                    <div class="titre">
                    	<h2>Présentation du site</h2>
                    </div>
                    <div class="corp">
						
                    	Ce site vous permet de gérer votre collection de jeux vidéo en ligne GRATUITEMENT.<br/>
						Présentez votre collection de jeux vidéo à travers votre profil, vos photos de collection, mais aussi en séléctionnant vos jeux favoris parmi les jeux déjà référencés.<br/><br/>
						Une section annonces vous permettra de "chiner" les bonnes affaires ou encore d'échanger vos doubles avec les autres membres.<br/>
						Vous aurez également accès à des actualités régulières sur tout ce qui touche de près ou de loin à l'univers du retrogaming.<br/><br/>
						100% gratuit RetroGamerStock est un site créé par un fan pour les fans de jeux vidéo rétro.
							
                    </div>
                    <div class="pied">
					</div>
				</div>
                  <div style="margin-top:20px;">
                	<h1>Dernier article</h1>
                </div>
				<?php 
				$sql = "SELECT * FROM articles,membres  WHERE articles.arAjouterPar = membres.membID AND arType=2 ORDER BY arID DESC LIMIT 1";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 2 news
                while($row = mysql_fetch_assoc($result))
                 {
				 $date = substr($row['arDate'],6,2)."/".substr($row['arDate'],4,2)."/".substr($row['arDate'],0,4);
                ?>						
                <div id="news">
                    <div class="titre">
                    	<h2><?php echo $row['arTitre'] ?></h2>
                    </div>
                    <div class="corp">
					
                    	<?php echo $row['arResume'];?>
						<a href="<?php echo fp_makeURL('news.php', $row["arID"]); ?>"><br/>[...]</a>
						
                    </div>
                    <div class="pied">
						<?php
						$arID = $row['arID'];
						$sql_count_comm = "SELECT count(comID) as nbCom FROM commentaires WHERE comIDArticle = '$arID' AND comActivation = '1'";
						$result_count_comm = mysql_query($sql_count_comm);
						$row_count_comm = mysql_fetch_assoc($result_count_comm);
						
						$sql_count_vues = "SELECT count(vueID) as nbVues FROM vues_article WHERE vueIDAr = '$arID'";
						$result_count_vues = mysql_query($sql_count_vues);
						$row_count_vues = mysql_fetch_assoc($result_count_vues)
						?>
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
							echo $row_count_vues["nbVues"]." vues</a> | ";
							}
						?>
						
						<a href="<?php echo fp_makeURL('news.php', $row["arID"]); ?>">Lire la suite</a><br />
                        Par <?php echo $row['membPseudo']." le ".$date." à ".$row["arHeure"]; ?>
                    </div>
                </div>
                <?php } ?>
				<br clear="all">
				          <div style="margin-top:20px;">
                	<img src="./img/retroplay.jpog" width="147" height="39" style="float:left;">
                </div>
				
                <div id="news">
                    <div class="titre">
                    	<h2>Vidéos retro par Oublard et la team du GrognPad</h2>
                    </div>
                    <div class="corp">
					
						<iframe width="170" height="135" src="http://www.youtube.com/v/Ph-lwf9VZ9Y"></iframe>
 	
						<iframe width="170" height="135" src="http://www.youtube.com/v/SBFQHwUViGI"></iframe>
<!--						<iframe width="160" height="130" src="http://www.youtube.com/v/pgXVSyqi3HQ"></iframe>
						<iframe width="160" height="130" src="http://www.youtube.com/v/0NDPV4wl8p8"></iframe>
						<iframe width="160" height="130" src="http://www.youtube.com/v/I6Ji1U3qdNk"></iframe>
-->
                    
					</div>
                    <div class="pied">
                    </div>
                </div>
					<br clear="all">
				
             
				<div style="margin-top:20px;">
					<h1>Petites annonces</h1>
                </div>
				<div id="news">
                    <div class="titre">
					</div>
					<div class="corp">
				<?php 
				$sql = "SELECT * FROM annonces,membres WHERE annonces.anAjouterPar = membres.membID AND anActivation ='1' ORDER BY anID DESC LIMIT 7";
				$repertoire_image = "./img/membres/annonces/";
				$result = mysql_query($sql);
				echo "<table>
						<tr>";
               
                while($row = mysql_fetch_assoc($result))
                 {	
					$image = $repertoire_image.$row["anID"].".".$row["anImg"];
					$membPseudo = $row["membPseudo"];
					$url_profil = fp_makeURL('profils.php', $row["membID"]);
              		echo "<td>
					<a class='fancybox' href='$image' data-fancybox-group='gallery' title=''><img src='$image' alt='' width='105px' height='80px'/></a><br/>
					<a href='$url_profil'><i>$membPseudo</i></a>
					</td>";		
				 } ?>
						</tr>
					</table>
                    </div>

           
				</div>
					<div class="pied">
					
                    </div>
                </div>
                <div id="stat">
				<div style="margin-top:20px;">
				
				<div style="margin-top:20px;">
					<h1>Photos de collection</h1>
                </div>
				<div id="news">
                    <div class="titre">
					</div>
					<div class="corp">
				<?php 
				$sql = "SELECT * FROM photos_collection,membres WHERE photos_collection.phIDColl = membres.membID ORDER BY phID DESC LIMIT 7";
				$repertoire_image = "./img/membres/collections/";
				$result = mysql_query($sql);
				echo "<table>
						<tr>";
               
                while($row = mysql_fetch_assoc($result))
                 {	
					$image = $repertoire_image.$row["phIDColl"]."_".$row["phNumero"].".".$row["phExt"];
					$membPseudo = $row["membPseudo"];
					$url_profil = fp_makeURL('profils.php', $row["membID"]);
              		echo "<td>
					<a class='fancybox' href='$image' data-fancybox-group='gallery' title=''><img src='$image' alt='' width='105px' height='80px'/></a><br/>
					<a href='$url_profil'><i>$membPseudo</i></a>
					</td>";		
				 } ?>
						</tr>
					</table>
                    </div>

           
				</div>
					<div class="pied"> 
                    </div>
                </div>
                <div id="stat">
				<div style="margin-top:20px;">
                    <h1>Statistiques</h1>
                </div>    
                       <div class="stat_left">
                            <div class="titre">
                                Top jeux
                            </div>
                            <div class="corp">
                            	<?php
								$query_jeux_populaires = "SELECT comporte.compIDJeu,consNom, count(*) as jeuxPopulaire,jeuNom FROM comporte,jeux,consoles 
								WHERE comporte.compIDJeu = jeux.jeuID 
								AND comporte.compIDCons = consoles.consID
								GROUP BY comporte.compIDJeu 
								ORDER BY jeuxPopulaire DESC LIMIT 10";
								$result_jeux_populaires = mysql_query($query_jeux_populaires) or die(mysql_error());
								$i=1;
								while($row_jeux_populaires = mysql_fetch_assoc($result_jeux_populaires)){
								echo "<b>".$i.".</b>&nbsp;&nbsp;";
								$i++;
									echo $row_jeux_populaires["jeuNom"]." sur <i><u>".$row_jeux_populaires["consNom"]."</u></i> (<b>".$row_jeux_populaires["jeuxPopulaire"]."</b>)<br/>";
								}
								?>
                            </div>
                            <div class="pied">
							
                            </div>                   
                    	</div>
                        
                        <div class="stat_right">
                                <div class="titre">
                                    Top collaborateurs
                                </div>
                                <div class="corp">
                                 <?php
									$sql_top_collaborateurs = "SELECT membres.membPseudo, count(jeux.jeuAjouterPar ) as Nb FROM jeux, membres WHERE jeux.jeuAjouterPar  = membres.membID AND jeuActivation ='1' GROUP BY 1 ORDER BY Nb DESC LIMIT 10";
									$result_top_collaborateurs = mysql_query($sql_top_collaborateurs) or die(mysql_error());
									$i=1;									
									while($row_top_collaborateurs = mysql_fetch_assoc($result_top_collaborateurs)) {
										echo "<b>".$i.".</b>&nbsp;&nbsp;";
										$i++;
										echo $row_top_collaborateurs["membPseudo"]." (<b>".$row_top_collaborateurs["Nb"]."</b>)<br/>";
									}
								?>
                                </div>
                                <div class="pied">
                                    
                                </div>
                        </div>  
   						
						<br clear="all">
						<br clear="all">
                       <div class="stat_left">
                            <div class="titre">
                                Nombres de visites
                            </div>
                            <div class="corp">
                            	<?php
								# NB VISISTES DU JOUR
								$date=date("Ymd");
								$query_nb_visites = "SELECT distinct(count(* )) as nbVisitesJour,visit_siteDate from visites_site where visit_siteDate = $date ";
								$result_nb_visites = mysql_query($query_nb_visites) or die(mysql_error());
								$row_nb_visites = mysql_fetch_assoc($result_nb_visites);
								echo "Aujourd'hui (<b>".$row_nb_visites["nbVisitesJour"]."</b>)<br/>";
								# VISITES RECORD
								$query_nb_visites = "SELECT distinct(count(*)) as nbVisitesRecord, visit_siteDate from visites_site group by visit_siteDate ORDER BY nbVisitesRecord DESC";
								$result_nb_visites = mysql_query($query_nb_visites) or die(mysql_error());
								$row_nb_visites = mysql_fetch_assoc($result_nb_visites);
								$date = substr($row_nb_visites['visit_siteDate'],6,2)."/".substr($row_nb_visites['visit_siteDate'],4,2)."/".substr($row_nb_visites['visit_siteDate'],0,4);
								echo "Record <i>le $date</i> (<b>".$row_nb_visites["nbVisitesRecord"]."</b>)<br/>";
								#VISITES TOTAL
								$query_nb_visites_total = "SELECT distinct(count(* )) as nbVisitesTotal from visites_site";
								$result_nb_visites_total = mysql_query($query_nb_visites_total) or die(mysql_error());
								$row_nb_visites_total = mysql_fetch_assoc($result_nb_visites_total);
								echo "Total <i>depuis le 06/02/2014</i> (<b>".$row_nb_visites_total["nbVisitesTotal"]."</b>)<br/>";
								?>
                            </div>
                            <div class="pied">
							</div>
						</div>
						<div class="stat_right">
                            <div class="titre">
                                Le site
                            </div>
                            <div class="corp">
                            	<?php
								# NB DE MEMBRES
								$query_nb_membres = "SELECT count(*) as nbMembres from membres ";
								$result_nb_membres = mysql_query($query_nb_membres) or die(mysql_error());
								$row_nb_membres = mysql_fetch_assoc($result_nb_membres);
								echo "Membres inscrits (<b>".$row_nb_membres["nbMembres"]."</b>)<br/>";
								# NB DE PHOTOS
								$query_nb_photos = "SELECT distinct(count(*)) as nbPhotos from photos_collection";
								$result_nb_photos = mysql_query($query_nb_photos) or die(mysql_error());
								$row_nb_photos = mysql_fetch_assoc($result_nb_photos);
								echo "Photos de collection (<b>".$row_nb_photos["nbPhotos"]."</b>)<br/>";
								# NB DE JEUX
								$query_nb_jeux = "SELECT count(*) as nbJeux from jeux WHERE jeuActivation = '1'";
								$result_nb_jeux = mysql_query($query_nb_jeux) or die(mysql_error());
								$row_nb_jeux = mysql_fetch_assoc($result_nb_jeux);
								echo "Jeux référencés (<b>".$row_nb_jeux["nbJeux"]."</b>)<br/>";
								?>
                            </div>
                            <div class="pied">
                            </div>                   
                    	</div>
				<br clear="all">
                <div style="margin-top:20px;">
                	<h1>Derniers jeux référencés</h1>
                </div>
				<div id="news">
				<?php
				$rep_img = './img/jeux/';
				echo "<marquee scrollamount='4'>";				
                $sql = "SELECT * FROM jeux,consoles,membres WHERE membres.membID = jeux.jeuAjouterPar AND jeux.jeuIDCons = consoles.consID AND jeuActivation = '1' ORDER BY jeuID DESC LIMIT 20";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 2 news
                while($row = mysql_fetch_assoc($result))
                 {
				$codeAffichage = $row["codeAffichage"];
				$membPseudo = $row["membPseudo"];
				$jeuNom = $row["jeuNom"];
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
				echo "<img src='$image' alt='$jeuNom' $type/> &nbsp; &nbsp;";
                 } ?>
				</marquee>
				</div>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>