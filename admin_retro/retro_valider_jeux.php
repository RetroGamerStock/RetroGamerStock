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
	// on teste l'existence de nos variables. On teste également si elles ne sont pas vides
	if (isset($_POST['btnAction'])) {

	$jeuID = mysql_real_escape_string($_POST["jeuID"]);
	$membID = $_SESSION["membID"];
	# Si il ne posséde pas la console on l'ajoute dans la table appartien
	$query_update ="UPDATE jeux 
					SET jeuActivation = '1' 
					WHERE jeuID = '$jeuID'
					";
	mysql_query($query_update) or die(mysql_error());
	mysql_close();
	}

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
        <h2>Validation des jeux</h2>
		<?php if (isset($erreur_ok)) echo $erreur_ok; else if (isset($erreur)) echo $erreur;?>          
            <form action="#" method="post">  
			<?php $query_select="	SELECT jeux.*,consoles.*,membres.membPseudo
								FROM jeux,consoles,membres
								WHERE jeux.jeuIDCons = consoles.consID
								AND jeux.jeuAjouterPar = membres.membID
								AND jeuActivation = '0'";
			$result_select = mysql_query($query_select) or die(mysql_error());
			
			# AFFICHAGE DE LA SELECTION
			#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE
			$rep_img = '../img/jeux/';
			$i = 0;
			if (mysql_num_rows($result_select) == 0)
			{
			echo "aucun jeux à valider";
			}
			while($row_select = mysql_fetch_assoc($result_select)) {
			$codeAffichage = $row_select["codeAffichage"];
			$consID = $row_select["jeuIDCons"];
			$jeuID = htmlentities($row_select["jeuID"]);
			$consID = htmlentities($row_select["jeuIDCons"]);
			$jeuNom = htmlentities($row_select["jeuNom"]);
			$jeuGenre = htmlentities($row_select["jeuGenre"]);
			$version = htmlentities($row_select["jeuVersion"]);
			$editeur = htmlentities($row_select["jeuEditeur"]);
			$developpeur = htmlentities($row_select["jeuDeveloppeur"]);
			$jeuDateSortie = substr($row_select['jeuDateSortie'],0,4);
			$image = $rep_img.$row_select["jeuID"].'.'.$row_select["jeuExtImg"];
			$jeuAjouterPar = htmlentities($row_select["membPseudo"]);
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
				//$image_version = $rep_img_version.$row_select["jeuVersion"].'.png';
				$i++;
				$anID = $row_select["anID"];
				$anTitre = $row_select["anTitre"];
				//$url = fp_makeURL('macollection.php', $consID);
						echo "                <div id='news'>
                    <div class='titre'>
                    	<h2>$anTitre</h2>
                    </div>
                    <div class='corp'>
					<table>
						<tr>
							<td width='200px'>	
									<form method = 'POST' action='#.php'>
									<input type='submit' name='btnAction' value='Valider' class='collec'>
									<input type='hidden' name='jeuID' value='$jeuID'>
									</form>
									
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	<form method = 'POST' action='retro_modifier_jeux.php'>
											<input type='submit' name='btnModifier' value='Modifier' class='collec'>
											<input type='hidden' name='jeuID' value='$jeuID'>
									<input type='hidden' name='consID' value='$consID'>
									</form>

							</td>
							<td width='200px'>
								<img src='$image' $type id='$jeuID'/><br/>
							</td>
							<td width='200px'>
								ID : <br/>
								Nom :<br/>
								Date de sortie :<br/>
								Genre :<br/>
								Editeur :<br/>
								Développeur :<br/>
								
							</td>
							<td width='200px'>
								<b>$jeuID</b><br/>
								<b>$jeuNom</b><br/>
								<b>$jeuDateSortie</b><br/>
								<b>$jeuGenre</b><br/>
								<b>$editeur</b><br/>
								<b>$developpeur</b><br/>
							</td>
						</tr>
						</table>
                    </div>
                    <div class='pied'>
					Ajouté par $jeuAjouterPar
                    </div>
                </div>"; 
				}
				?>				
              <br />
              <br />
</form>
		</div>
        </div>
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