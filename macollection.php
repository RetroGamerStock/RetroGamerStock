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
if(empty($_GET["x"]))
{
	echo "<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>Ma collection</h1>
				</div>";
	$query_select="	SELECT consoles.*,editeurs.* FROM collections,appartient,consoles,editeurs	WHERE collections.collID = appartient.appartIDColl AND appartient.appartIDCons = consoles.consID AND consoles.consFabricant = editeurs.editID AND collections.collIDMemb = $membID";
	$result_select = mysql_query($query_select) or die(mysql_error());	
	# AFFICHAGE DE LA SELECTION
	#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE
	$rep_img = './img/consoles/';
	$i = 0;
	while($row_select = mysql_fetch_assoc($result_select)) {
		$i++;
		$consID = $row_select["consID"];
		$url = fp_makeURL('macollection.php', $consID);
		$consNom = $row_select["consNom"];
		$editeur = $row_select["editNom"];
		$consDateSortie = substr($row_select['consDateSortie'],6,2)."/".substr($row_select['consDateSortie'],4,2)."/".substr($row_select['consDateSortie'],0,4);
		$image = $rep_img.$row_select["consID"].'.'.$row_select["consExtImg"];	
		
		# COUNT LE NOMBRE DE JEUX SUR CETTE CONSOLE
		$query_count="SELECT count(comporte.compIDJeu) as nbJeux FROM comporte WHERE comporte.compIDColl = $membID AND comporte.compIDCons = $consID GROUP BY comporte.compIDCons";
		$result_count = mysql_query($query_count) or die(mysql_error());
		$row_count = mysql_fetch_assoc($result_count);
		$nbJeux = $row_count["nbJeux"];

			echo "<div id='news'>
                    <div class='titre'>
                    	<h2>$consNom</h2>
                    </div>
                    <div class='corp'>
						<table>
						<tr>
							<td width='200px'>
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
                  ";
						if($nbJeux>0){
						echo "<a href=$url>Voir vos <b>$nbJeux</b> jeux.</a>";
						}
						else{
							echo "Vous ne possédez aucun jeu.";
						}
            echo"   </div>
                </div>";
	}
	echo "</div>";
}
else{
		$consID = fp_getURL($_GET["x"]);
		$query_select_console =" SELECT consNom
								FROM consoles
								WHERE consID = $consID
								";
		$result_select_console = mysql_query($query_select_console) or die(mysql_error());
		$row_select_console = mysql_fetch_assoc($result_select_console);
		$consNom = $row_select_console["consNom"];
		echo "<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>Ma collection sur $consNom</h1>
				</div>";		
		

	$query_select="	SELECT jeux.*,consoles.*,comporte.* FROM 
					collections,comporte,jeux,consoles
					WHERE collections.collID = comporte.compIDColl 
					AND comporte.compIDJeu = jeux.jeuID 
					AND consoles.consID = jeux.jeuIDCons
					AND collections.collIDMemb = $membID 
					AND comporte.compIDCons = $consID
					ORDER BY jeuNom ASC
					";
	$result_select = mysql_query($query_select) or die(mysql_error());	
	# AFFICHAGE DE LA SELECTION
	#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE

	$rep_img = './img/jeux/';
	$i=0;
	while($row_select = mysql_fetch_assoc($result_select)) {
	$i++;
		if($row_select["favori"] == 1){$favori="checked=checked";}else{$favori="";};
		$etat = $row_select["etat"];
		$condition = $row_select["condition"];
		$nbExemplaire = $row_select["nbExemplaire"];
		$consID = $row_select["jeuIDCons"];
		$codeAffichage = $row_select["codeAffichage"];
		$jeuID = htmlentities($row_select["jeuID"]);
		$jeuNom = htmlentities($row_select["jeuNom"]);
		$editeur = htmlentities($row_select["jeuEditeur"]);
		$developpeur = htmlentities($row_select["jeuDeveloppeur"]);
		$jeuGenre = htmlentities($row_select["jeuGenre"]);
		$jeuDateSortie = substr($row_select['jeuDateSortie'],0,4);
		$image = $rep_img.$row_select["jeuID"].'.'.$row_select["jeuExtImg"];
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

				echo "<div id='news'>
                    <div class='titre'>
                    	<h2>$jeuNom</h2>
                    </div>
                    <div class='corp'>
						<table>
							<tr>
								<td width='230px'>
									<table>
										<tr>
											<td width='100px'>
											<form method = 'POST' action='modifier_comporte.php'>
											Exemplaire :
											</td>
											<td width='100px'>
											<input type='text' name='exemplaire' size='1' value='$nbExemplaire' maxlength='1' width='96' style='width:96px;'>
											</td>
										</tr>
										<tr>
											<td>
												Etat : 
											</td>
											<td>
												<select name='etat' width='100' style='width:100px;'>";
												?>
												<option value='0' <? if($etat == 0){echo " selected";} ?> > -</option>
												<option value='1' <? if($etat == 1){echo " selected";} ?> > Blister</option>
												<option value='2' <? if($etat == 2){echo " selected";} ?> > Comme neuf</option>
												<option value='3' <? if($etat == 3){echo " selected";} ?> > Bon etat</option>
												<option value='4' <? if($etat == 4){echo " selected";} ?> > Moyen</option>
												<option value='5' <? if($etat == 5){echo " selected";} ?> > Mauvais</option>
												<?php
				echo "							</select>
											</td>
										</tr>
										<tr>
											<td>
												Condition : 
											</td>
											<td>
												<select name='condition' width='100' style='width:100px;'>";
												?>
												<option value='0' <? if($condition == 0){echo " selected";} ?> > -</option>
												<option value='1' <? if($condition == 1){echo " selected";} ?> > Complet</option>
												<option value='2' <? if($condition == 2){echo " selected";} ?> > Boite</option>
												<option value='3' <? if($condition == 3){echo " selected";} ?> > Notice</option>
												<option value='4' <? if($condition == 4){echo " selected";} ?> > Loose</option>
												<?php
				echo "							</select>
											</td>
										</tr>
										<tr>
											<td>
												Favori : 
											</td>
											<td>
											<input type='checkbox' name='favori' $favori>
											</td>
										</tr>
										<tr>
											<td colspan='2'>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='btnModifier' value='Modifier' style='width:100px'>
													<input type='hidden' name='jeuID' value='$jeuID'>
													<input type='hidden' name='consID' value='$consID'>
												</form>
												<div style='float:left'>
												<form method = 'POST' action='supprimer_comporte.php'>
													<input type='submit' name='btnSupprimer' value='Supprimer' style='width:100px'>
													<input type='hidden' name='jeuID' value='$jeuID'>
													<input type='hidden' name='consID' value='$consID'>
												</form>
												</div>
											</td>
										</tr>
									</table>
								</td>
								<td width='200px'>
									<img src='$image' $type class='zoom' id='$jeuID'/><br/>
								</td>
								<td width='200px'>
								Nom :<br/>
								Date de sortie :<br/>
								Genre :<br/>
								Editeur :<br/>
								Développeur :
							</td>
							<td width='200px'>
								<b>$jeuNom</b><br/>
								<b>$jeuDateSortie</b><br/>
								<b>$jeuGenre</b><br/>
								<b>$editeur</b><br/>
								<b>$developpeur</b>
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
                </div>";
			
}



echo "</div>";
}
}
else
{
echo "<h1>Zone reservée</h1>
		<div class='erreur'> 
		Cette zone est réservée aux membres !!<br>
		 <a href='http://retrogamerstock.fr/inscription.php'>Inscrivez-vous</a> GRATUITEMENT ou <a href='http://www.retrogamerstock.fr/connexion.php'>identifiez-vous</a>
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