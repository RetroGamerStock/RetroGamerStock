<?php
session_start();
//Script par Zelix pour Retrogamerstock

//on vérifie si les 2 sessions sont présentes
 require "../php/parametres.php";
 connexion_bd();
  //on va chercher tout ce qui correspond à l'utilisateur
 $affiche = mysql_query("SELECT * FROM membres WHERE membPseudo='".mysql_real_escape_string(stripcslashes($_SESSION['login']))."' AND membType='".mysql_real_escape_string(2)."'");
 $result = mysql_fetch_assoc($affiche);

 //http://php.net/manual/fr/function.extract.php
 extract($result);
 //on libère le résultat de la mémoire
 mysql_free_result($affiche);
if(isset($_SESSION['login']) && isset($_SESSION['mdp']) && $result['membType'] == 2){

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="fr" />
<title>Configuration - <?php echo $nom_site?></title>
<link rel="stylesheet" href="../css/styles.css" type="text/css" media="screen, print, handheld" />
<link rel="shortcut icon" href="../favicon.ico" type="image/vnd.microsoft.icon" />	
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" /><![endif]-->
</head>
 
<body>
		<div id="global">
        	<?php include('../inc/header.php'); ?> 
			<?php include('../inc/menu_admin.php'); ?> 
            <div id="main_admin">
            	<div id="main_content_admin">
		<?php
		if((isset($_POST["btnModifier"])) || (isset($_POST["btnValider"])))
		{
		$rep_img = '../img/jeux/';
		$rep_img_version = '../img/';
		$jeuID = $_POST["jeuID"];
		$query_jeu = "SELECT * FROM jeux,consoles WHERE jeux.jeuIDCons = consoles.consID AND jeuID = '$jeuID'";
		$result_jeu = mysql_query($query_jeu) or die(mysql_error());
		$row_jeu = mysql_fetch_assoc($result_jeu);
		$jeuNom = $row_jeu["jeuNom"];
		$jeuEditeur = $row_jeu["jeuEditeur"];
		$jeuDeveloppeur = $row_jeu["jeuDeveloppeur"];
		$jeuGenre = $row_jeu["jeuGenre"];
		$jour = substr($row_jeu['jeuDateSortie'],6,2);
		$mois = substr($row_jeu['jeuDateSortie'],4,2);
		$année = substr($row_jeu['jeuDateSortie'],0,4);
		$image = $rep_img.$row_jeu["jeuID"].'.'.$row_jeu["jeuExtImg"];
		$codeAffichage = $row_console["codeAffichage"];
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
				
		?>
		
		
		<h1>Modifier le jeu <?php echo $jeuNom; ?></h1><br/>
		<form enctype='multipart/form-data' method='POST' action='#'>
			<div class="inscri">
			<label>Identifiant :</label>
			<input type='text' value="<?php if (isset($_POST["jeuID"])) {echo $_POST["jeuID"];}else{echo $jeuID;} ?>" name='jeuID' size='2' class="inscription" readonly>
			</div>
			<div class="inscri">
			<label>Titre du jeu *:</label>
			<input type='text' value="<?php if (isset($_POST["jeuNom"])) {echo $_POST["jeuNom"];}else{echo $jeuNom;} ?>" name='jeuNom' maxlength='45' size='45' class="inscription">
			</div>
			<div class="inscri">
			<label>Console *:</label>
			<select name="consoleID" class="inscription">
					<option value=''></option>
		<?php
	$query_console = "SELECT * FROM consoles";
	$result_console = mysql_query($query_console) or die(mysql_error());
	$row_console = mysql_fetch_assoc($result_console);
	while($row_console = mysql_fetch_assoc($result_console)){
		
		echo "<option value='".$row_console["consID"]."' ";
		//echo ">".$row_select["consID"]."</option>";
			
				if($row_jeu["jeuIDCons"] == $row_console["consID"])
				{
				echo " selected";
				}
			
		echo ">".$row_console["consNom"]."</option>";
	
	}
	?>	
			</select> 
			</div>
			<div class="inscri">
			<label>Editeur *: </label>
			<input type='text' value="<?php if (isset($_POST["jeuEditeur"])) {echo $_POST["jeuEditeur"];}else{echo $jeuEditeur;} ?>" name='jeuEditeur' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Développeur *:</label>
			<input type='text' value="<?php if (isset($_POST["jeuDeveloppeur"])) {echo $_POST["jeuDeveloppeur"];}else{echo $jeuDeveloppeur;} ?>" name='jeuDeveloppeur' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Genre *:</label>
			<input type='text' value="<?php echo $jeuGenre; ?>" name='jeuGenre' maxlength='25' size='25' class='inscription'>
			</div>
			<div class="inscri">
			<label>Version *:</label>
				<select name='jeuVersion' class='inscription'>
						<option value='1' <?php if($row_jeu["jeuVersion"]==1){echo " selected";} ?> >Européenne</option>
						<option value='2' <?php if($row_jeu["jeuVersion"]==2){echo " selected";} ?> >Japonaise</option>
						<option value='3' <?php if($row_jeu["jeuVersion"]==3){echo " selected";} ?> >Amércaine</option>
					</select>
			</div>

			<div class="inscri">
			<label>Date de sortie :</label>
			<input type='text' value="<?php echo $jour; ?>" name='numJour' maxlength='2' size='2' class='inscription' style='width:50px;'> <input type='text' value="<?php echo $mois; ?>" name='numMois' maxlength='2' size='2' class='inscription' style='width:50px;'> <input type='text' value="<?php echo $année; ?>" name='numAnnee' maxlength='4' size='2' class='inscription' style='width:50px;'>
			</div><br/><center>
			</div>
			<div class="inscri">
			<label>Image :</label>
			<img src="<?php echo $image;?>" <?php echo $type;?> id="<?php echo $jeuID;?>"/>
			</div><br>
			<div class="inscri">
					<input type='hidden' name='MAX_FILE_SIZE' value='153200'>
					<input type='file' name='jeuImg' size='45' class="inscription">
			</div><br/>
			</center>
			<div class="inscri">
			<input type='submit' name='btnValider' value='Confirmer' class='boutton'>
			</div>
			<span style="color:#F00;font-weight:bold;">Remplissez les champs obligatoires</span>   
		</form>
		<?php		
		if(isset($_POST['btnValider']) && $_POST['btnValider']=='Confirmer'){
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
			
			else
			{
				$jeuNom = mysql_real_escape_string($_POST["jeuNom"]);
				$jeuID = mysql_real_escape_string($_POST["jeuID"]);
				$consID = mysql_real_escape_string($_POST["consoleID"]);
				$jeuDeveloppeur = mysql_real_escape_string($_POST["jeuDeveloppeur"]);
				$jeuEditeur = mysql_real_escape_string($_POST["jeuEditeur"]);
				$jeuDateSortie = mysql_real_escape_string($_POST["numAnnee"]).mysql_real_escape_string($_POST["numMois"]).mysql_real_escape_string($_POST["numJour"]);
				$jeuGenre = mysql_real_escape_string($_POST["jeuGenre"]);
				$jeuVersion = mysql_real_escape_string($_POST["jeuVersion"]);
				$jeuAjouterPar = $_SESSION["membID"]; 
				$MAJ_Ext = substr($Ext,1);
				$query_update = "update jeux
					set jeuIDCons = '$consID',
					jeuNom = '$jeuNom',
					jeuEditeur ='$jeuEditeur',
					jeuDeveloppeur = '$jeuDeveloppeur',
					jeuDateSortie = '$jeuDateSortie',
					jeuGenre = '$jeuGenre',
					jeuVersion = '$jeuVersion'
					where jeuID = '$jeuID';
					";
				/*$query_insert="INSERT INTO jeux 
							  (jeuID,jeuIDCons,jeuNom,jeuEditeur,jeuDeveloppeur,jeuDateSortie,jeuVersion,jeuExtImg,jeuAjouterPar,jeuGenre,jeuActivation)
						VALUES('$numero','$consID','$jeuNom','$jeuEditeur','$jeuDeveloppeur','$jeuDateSortie','$jeuVersion','$MAJ_Ext','$jeuAjouterPar','$jeuGenre','0')
									";*/
									
				mysql_query($query_update) or die(mysql_error());
				mysql_close();
				echo '<div class="ok"><center>Le jeu à été modifié avec succé, vous allez être redirigé...</center></div>
					<script type="text/javascript"> window.setTimeout("location=(\'retro_modifier_jeux.php\');",3000) </script>';
			}
		}

		}
		
		
		else
		{
		?>
		
       <h1>Modifier un jeu</h1>		
			<form method = 'POST' action='#'>
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
		<br/>
	
		
		<?php
	# RECHERCHE CONCERNANT UN JEU
	if((isset($_POST["txtRechercheJeu"])) || (!empty($_SESSION["txtRechercheJeu"])) || (!empty($_SESSION["txtTriConsole"])))
	{
			if(isset($_POST["txtRechercheJeu"]))
			{
			$jeuNomRecherche = mysql_real_escape_string($_POST["txtRechercheJeu"]);
			$consIDRecherche = mysql_real_escape_string($_POST["txtTriConsole"]);
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
			$query_select_jeu.="ORDER BY jeuID ASC
							";
							
			$result_select_jeu = mysql_query($query_select_jeu) or die(mysql_error());

			$rep_img = '../img/jeux/';
			$rep_img_version = '../img/';
			$i = 0;
			# SI IL N'Y A PAS DE Résultat on met un message d'erreur
			if (mysql_num_rows($result_select_jeu) == 0)
			{
			echo "<div class='erreur'>Aucun résultat.<br/>
					Vérifiez l'orthographe du jeu.<br/>
					S'il n'existe pas vous pouvez l'ajouter via le formulaire <a href='http://www.retrogamerstock.fr/referencer.php'>ajouter un jeu</a>
					</div>";
			}
			while($row_select_jeu = mysql_fetch_assoc($result_select_jeu)) {
			$i++;
			$codeAffichage = $row_select_jeu["codeAffichage"];
			$consID = $row_select_jeu["jeuIDCons"];
			$jeuID = htmlentities($row_select_jeu["jeuID"]);
			$consID = htmlentities($row_select_jeu["jeuIDCons"]);
			$consNom = htmlentities($row_select_jeu["consNom"]);
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
	

					echo "<div id='news'>
							<div class='titre'>
								<h2>$jeuNom</h2>
							</div>
							<div class='corp'>
								<table>
						<tr>
							<td width='200px'>
								<form method = 'POST' action='#'>
									<input type='submit' name='btnModifier' value='Modifier' class='collec'>
									<input type='hidden' name='jeuID' value='$jeuID'>
									<input type='hidden' name='consID' value='$consID'>
									<input type='hidden' name='txtRechercheJeu' value='$jeuNomRecherche'>
									<input type='hidden' name='txtTriConsole' value='$consIDRecherche'>
								</form>
							</td>
							<td width='200px'>
								<img src='$image' $type id='$jeuID'/><br/>
							</td>
							<td width='200px'>
								ID :<br/>
								Nom :<br/>
								Date de sortie :<br/>
								Genre :<br/>
								Editeur :<br/>
								Développeur :<br/>
								Console :<br/>
								
							</td>
							<td width='200px'>
								<b>$jeuID</b><br/>
								<b>$jeuNom</b><br/>
								<b>$jeuDateSortie</b><br/>
								<b>$jeuGenre</b><br/>
								<b>$editeur</b><br/>
								<b>$developpeur</b><br/>
								<b>$consNom</b><br/>
							</td>
						</tr>
						</table>
					</div>
					<div class='pied'>
                </div>
				</div>
			";
			}
		}	
		}
		 ?>
    <?php include('../inc/footer.php');?>
    </div> 
    </div>
</body>
</html>
 <?php
 //fermeture de la BD
 close_bd();
 //on boucle la session du haut de page
}
 else 
 {
  echo 'RIEN A VOIR!!!<script type="text/javascript"> window.setTimeout("location=(\'../index.php\');",3000) </script>'; return false;
 } 
?>