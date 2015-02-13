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
    		<h1>
			Ludothèque
			</h1>
			<?php
			$query_select=" SELECT * FROM consoles ORDER BY consNom ASC";
			$result_select = mysql_query($query_select) or die(mysql_error());
			?>

			<p>
			Cette section vous permet de voir les jeux et consoles qui sont référencées sur le site.<br>
			En tant que membre vous avez la possibilité de les ajouter à votre collection.<br>
			</p><br>
			<form method = 'POST' action='#'>
			Console : 
			<input type='text' value="<?php if (!empty($_POST["txtRechercheConsole"])) { echo stripcslashes(htmlspecialchars($_POST["txtRechercheConsole"],ENT_QUOTES)); } ?>" name='txtRechercheConsole' size='25'>
			<input type='submit' name='btnRechercher' value='Rechercher'>
			</form>
			<?php
			# ON RECUPERE LA VALEUR DE LA RECHERCHE SUR LE JEU ET ON L'AFFICHE DANS LE CHAMP
			if(isset($_POST["txtRechercheJeu"])){$valueJeu = mysql_real_escape_string($_POST["txtRechercheJeu"]);}
			elseif(!empty($_SESSION["txtRechercheJeu"])){ $valueJeu = $_SESSION["txtRechercheJeu"]; }
			else { $valueJeu = '';}
			if(isset($_POST["txtTriConsole"])){$valueConsole = mysql_real_escape_string($_POST["txtTriConsole"]);}
			elseif(!empty($_SESSION["txtTriConsole"])){ $valueConsole = $_SESSION["txtTriConsole"]; }
			else { $valueConsole = '';}
			?>

			
			<form method = 'POST' action='ludotheque.php'>
					Rechercher un jeu: 
					<input type='text' value="<?php if (!empty($_POST["txtRechercheJeu"])) { echo stripcslashes(htmlspecialchars($_POST["txtRechercheJeu"],ENT_QUOTES)); } ?>" name='txtRechercheJeu' size='25'>
					<select name='txtTriConsole'>
					<option value=''>Toutes les consoles</option>
			<?php
			while($row_select = mysql_fetch_assoc($result_select)) {
			$consID = $row_select["consID"];
				echo "<option value='$consID'";
					if($consID == $valueConsole)
					{
					echo "selected";
					}
				echo ">".$row_select["consNom"]."</option>";
			}
			?>
					</select>
					<input type='submit' name='btnRechercher' value='Rechercher'>
				</form>
		<br>
	
		
		<?php
	# RECHERCHE CONCERNANT UN JEU
	if((isset($_POST["txtRechercheJeu"])) || (!empty($_SESSION["txtRechercheJeu"])) || (!empty($_SESSION["txtTriConsole"])))
	{
			if(isset($_POST["txtRechercheJeu"]))
			{
			$jeuNomRecherche = mysql_real_escape_string($_POST["txtRechercheJeu"]);
			$consIDRecherche = mysql_real_escape_string($_POST["txtTriConsole"]);
			}
			elseif((!empty($_SESSION["txtRechercheJeu"])) || (!empty($_SESSION["txtTriConsole"])))
			{
			
			$jeuNomRecherche = mysql_real_escape_string($_SESSION["txtRechercheJeu"]);
			$consIDRecherche = mysql_real_escape_string($_SESSION["txtTriConsole"]);
			# REINITIALISATION DES VARIABLES
			$_SESSION["txtRechercheJeu"] = '';
			$_SESSION["txtTriConsole"] = '';
			
			}
			
			$query_select_jeu="	SELECT jeux.*,consoles.*
								FROM jeux,consoles
								WHERE jeux.jeuIDCons = consoles.consID
								AND jeuNom like '%$jeuNomRecherche%'
							";
			if(!empty($consIDRecherche))
			{
				$query_select_jeu.="AND jeuIDCons = '$consIDRecherche'
							";
			}
			$query_select_jeu.="AND jeuActivation = '1' ORDER BY jeuNom ASC
							";
							
			$result_select_jeu = mysql_query($query_select_jeu) or die(mysql_error());

			$rep_img = './img/jeux/';
			$rep_img_version = './img/';
			$i = 0;
			# SI IL N'Y A PAS DE Résultat on met un message d'erreur
			if (mysql_num_rows($result_select_jeu) == 0)
			{
			echo "<div class='erreur'>Aucun résultat.<br>
					Vérifiez l'orthographe du jeu.<br>
					S'il n'existe pas vous pouvez l'ajouter via le formulaire <a href='http://www.retrogamerstock.fr/referencer.php'>ajouter un jeu</a>
					</div>";
			}
			while($row_select_jeu = mysql_fetch_assoc($result_select_jeu)) {
			$i++;
			$codeAffichage = $row_select_jeu["codeAffichage"];
			$consID = $row_select_jeu["jeuIDCons"];
			$jeuID = htmlentities($row_select_jeu["jeuID"]);
			$consID = htmlentities($row_select_jeu["jeuIDCons"]);
			$jeuNom = htmlentities($row_select_jeu["jeuNom"]);
			$jeuGenre = htmlentities($row_select_jeu["jeuGenre"]);
			$version = htmlentities($row_select_jeu["jeuVersion"]);
			$editeur = htmlentities($row_select_jeu["jeuEditeur"]);
			$developpeur = htmlentities($row_select_jeu["jeuDeveloppeur"]);
			$jeuDateSortie = substr($row_select_jeu['jeuDateSortie'],0,4);
			$image = $rep_img.$row_select_jeu["jeuID"].'.'.$row_select_jeu["jeuExtImg"];
			# ON COMPTE COMBIEN DE MEMBRE POSSEDE CE JEU
			$query_count = "SELECT count(*) as nbJeux FROM comporte WHERE compIDJeu = '$jeuID'";
			$result_count = mysql_query($query_count) or die(mysql_error());
			$row_count = mysql_fetch_assoc($result_count);
			$nbJeux = $row_count["nbJeux"];
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
				//$image_version = $rep_img_version.$row_select_jeu["jeuVersion"].'.png';
				if((isset($_SESSION["membID"]))&&($_SESSION["membActivation"] == 1))
					{
					$query_count="	SELECT count(*) as nbJeux
									FROM comporte
									WHERE comporte.compIDJeu = '$jeuID'
									AND comporte.compIDColl =  '$membID'
											";
					$result_count = mysql_query($query_count) or die(mysql_error());
					$row_count = mysql_fetch_assoc($result_count);
					}	

					echo "<div id='news'>
							<div class='titre'>
								<h2>$jeuNom</h2>
							</div>
							<div class='corp'>
								<table>
						<tr>";
							if((isset($_SESSION["membID"]))&&($_SESSION["membActivation"] == 1))
							{
								if($row_count["nbJeux"]==0)
								{															
									echo "<td width='200px'>
											<form method = 'POST' action='ajouter_comporte.php'>
												<input type='submit' name='btnAjouter' value='Ajouter à ma collection' class='collec'>
												<input type='hidden' name='jeuID' value='$jeuID'>
												<input type='hidden' name='consID' value='$consID'>
												<input type='hidden' name='txtRechercheJeu' value='$jeuNomRecherche'>
												<input type='hidden' name='txtTriConsole' value='$consIDRecherche'>
											</form>
										</td>
									";
								}
								else
								{
									echo "<td width='200px'>
									</td>";
								}
							}
					echo "
							<td width='200px'>
								<img src='$image' $type id='$jeuID'/><br/>
							</td>
							<td width='200px'>
								Nom :<br/>
								Date de sortie :<br/>
								Genre :<br/>
								Editeur :<br/>
								Développeur :<br/>
								
							</td>
							<td width='200px'>
								<b>$jeuNom</b><br/>
								<b>$jeuDateSortie</b><br/>
								<b>$jeuGenre</b><br/>
								<b>$editeur</b><br/>
								<b>$developpeur</b><br/>
							</td>
						</tr>
						</table>
					</div>
					<div class='pied'>";
					if($nbJeux >1)
					{echo "$nbJeux membres possédent ce jeu.";}
					else
					{echo "$nbJeux membre posséde ce jeu.";}
					echo "</div>
                </div>
			";
			}
		}
		# RECHERCHE CONCERNANT UNE CONSOLE
		elseif((isset($_POST["txtRechercheConsole"]))  || (!empty($_SESSION["txtRechercheConsole"])))
		{
			
			if(isset($_POST["txtRechercheConsole"]))
			{
			$consNomRecherche = mysql_real_escape_string($_POST["txtRechercheConsole"]);
			}
			elseif(!empty($_SESSION["txtRechercheConsole"]))
			{
			$consNomRecherche = $_SESSION["txtRechercheConsole"];
			# REINITIALISATION DE LA VARIABLE DE SESSION
			$_SESSION["txtRechercheConsole"] = '';
			}
			$query_select_console="	SELECT consoles.*,editeurs.* 
							FROM consoles,editeurs	
							WHERE consoles.consFabricant = editeurs.editID 
							AND consNom like '%$consNomRecherche%'
							";
			$result_select_console = mysql_query($query_select_console) or die(mysql_error());
			$rep_img = './img/consoles/';
			$i = 0;
			# MESSAGE SI IL N'Y A PAS DE RESULTAT
			if (mysql_num_rows($result_select_console) == 0)
			{
			echo "<div class='erreur'>Aucun résultat</div>";
			}
			while($row_select_console = mysql_fetch_assoc($result_select_console)) {
				$i++;
				$consID = $row_select_console["consID"];
				//$url = fp_makeURL('macollection.php', $consID);
				$consNom = $row_select_console["consNom"];
				$editeur = $row_select_console["editNom"];
				$consDateSortie = substr($row_select_console['consDateSortie'],6,2)."/".substr($row_select_console['consDateSortie'],4,2)."/".substr($row_select_console['consDateSortie'],0,4);
				$image = $rep_img.$row_select_console["consID"].'.'.$row_select_console["consExtImg"];	
				# SI C'EST UN MEMBRE ON LUI PERMET D'AJOUTER LA CONSOLE A SA COLLECTION SI IL NE LA POSSEDE PAS DEJA
								if(isset($_SESSION["membID"]))
								{
								$query_count="	SELECT count(*) as nbConsole
														FROM appartient
														WHERE appartient.appartIDCons = '$consID'
														AND appartient.appartIDColl =  '$membID'
														";
								$result_count = mysql_query($query_count) or die(mysql_error());
								$row_count = mysql_fetch_assoc($result_count);
								}	

					echo "<div id='news'>
							<div class='titre'>
								<h2>$consNom</h2>
							</div>
							<div class='corp'>
								<table>
									<tr>
								";
								if(isset($_SESSION["membID"]))
								{
									if($row_count["nbConsole"]==0)
									{															
										echo "<td width='200px'>
										<form method = 'POST' action='ajouter_appartient.php'>
										<input type='submit' name='btnAjouter' value='Ajouter à ma collection' class='collec'>
										<input type='hidden' name='consID' value='$consID'>
										<input type='hidden' name='txtRechercheConsole' value='$consNomRecherche'>
										</form>
										</td>
										";
									}
									else
									{
										echo "<td width='200px'>
										</td>";
									}
								}
								else{
									echo "<td width='200px'></td>";
								}
								echo "	<td width='200px'>
										<img src='$image' width='48px' height='48px'/><br/>
									</td>
									<td width='200px'>
										Nom :<br/>
										Date de sortie :<br/>
										Fabricant :<br/>
									</td>
									<td width='200px'>
										<b>$consNom</b><br/>
										<b>$consDateSortie</b><br/>
										<b>$editeur</b><br/>
								</td>
							</tr>
						</table>
					</div>
					<div class='pied'>
					</div>
                </div>";
				
			}
		}
		
		 
		 ?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>