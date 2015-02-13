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

if(empty($_GET["x"]))
{

echo "<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>Les membres</h1>
				</div>
		";
# FORMULAIRE DE RECHERCHE
/*if(isset($_POST["txtRecherche"]))
{
$valueCollection = mysql_real_escape_string($_POST["txtRecherche"]);
}
else
{
$valueCollection = '';
}
	echo "<div class='inscri'>
				<form method = 'POST' action='collections.php'>

						<label>Rechercher un membre :</label>

						<input type='text' value='$valueCollection' name='txtRecherche' size='25' class='inscription'>

						<input type='submit' name='btnRechercher' value='Rechercher' class='boutton'>

					</form>
		</div>
		
	";
	*/$query_select="select membres.*,
count(DISTINCT(appartient.appartIDCons)) as nbCons,
count(DISTINCT(comporte.compIDJeu)) as nbJeu
 from membres

	left join appartient	
	on membres.membID = appartient.appartIDColl
	left join comporte
	on membres.membID = comporte.compIDColl
	WHERE membres.membActivation = '1'
";
/*
$query_select="select membres.*,count(DISTINCT(appartient.appartIDCons)) as nbCons,
count(DISTINCT(comporte.compIDJeu)) as nbJeu,


from membres,collections,appartient,comporte
where membres.membID = collections.collID
and collections.collID = appartient.appartIDColl
and collections.collID = comporte.compIDColl
and membres.membActivation ='1'";

*/


	# SI LE CHAMP RECHERCHER EST RENSEIGNER
	if(isset($_POST["txtRecherche"])){
	$txtRecherche = mysql_real_escape_string($_POST["txtRecherche"]);
	$query_select.="AND membres.membPseudo like '%$txtRecherche%'
					";
	}				
	$query_select.="GROUP BY 1,2
					ORDER BY nbJeu DESC
					";
	$result_select = mysql_query($query_select) or die(mysql_error());	
	$i = 0;
	$rep_img = './img/membres/';
	# AFFICHAGE DE LA SELECTION

	while($row_select = mysql_fetch_assoc($result_select)) {

	$i++;
	$url_collection = fp_makeURL('collections.php', $row_select["membID"]);
	$url_profil = fp_makeURL('profils.php', $row_select["membID"]);
	$membPseudo = htmlentities($row_select["membPseudo"]);
	$DateConnection = substr($row_select["membDateConnection"],6,2)."/".substr($row_select["membDateConnection"],4,2)."/".substr($row_select["membDateConnection"],0,4);
	$nbJeu = $row_select["nbJeu"];
	//$nbVisites = $row_select["nbVisites"];
	$nbCons = $row_select["nbCons"];
	$dateInscription = substr($row_select['membDateInscription'],6,2)."/".substr($row_select['membDateInscription'],4,2)."/".substr($row_select['membDateInscription'],0,4);
	# Si l'utilisateur n'a pas d'image alors on lui met une image par default
	if(!empty($row_select["membImg"]))
	{$image = $rep_img.$row_select['membID'].'.'.$row_select['membImg'];}
	else
	{$image = $rep_img.'0.jpg';}
			echo "<div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'>
                    	<table>
							<tr>
								<td width='200px'>
									<img src='$image' width='100px' height='91px'>
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
									<b>$nbCons</b><br>
									<b>$nbJeu</b><br>
									<b>$DateConnection</b><br>";
									/*if($nbVisites > 0)
									{echo "<b>$nbVisites</b><br>";}
									else
									{{echo "<b>0</b><br>";}}*/
			echo "				</td>
							</tr>						
						</table>
                    </div>
                    <div class='pied'>
                    	<a href='$url_profil'>Profil</a>";
						if($nbCons > 0)
						{echo " - <a href='$url_collection'>Collection</a>";}
            echo"   </div>
                </div>";
			
	}
}
#SI IL Y A DES PARAMETRES ON AFFICHE
else
{	

	#LA LISTE DES CONSOLES SI ON A JUSTE L'ID DE LA COLLECTION
	$Params = fp_getURL($_GET["x"]);
	if(count($Params)==1){

		$collID = $Params;
		include('inc/visites_collection.php');
		// Ajout d'une visite pour cette collection
		include('inc/visites_collection.php');
		$query_select_pseudo =" SELECT membPseudo
								FROM membres
								WHERE membID = $collID
								";
		$result_select_pseudo = mysql_query($query_select_pseudo) or die(mysql_error());
		$row_select_pseudo = mysql_fetch_assoc($result_select_pseudo);
		$membPseudo = $row_select_pseudo["membPseudo"];
		echo "
		<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>La collection de $membPseudo</h1>
				</div>
		";		
		$query_select="	SELECT collID,consoles.*,editeurs.* 
						FROM collections,appartient,consoles,editeurs 
						WHERE collections.collID = appartient.appartIDColl 
						AND appartient.appartIDCons = consoles.consID 
						AND consoles.consFabricant = editeurs.editID 
						AND collections.collID = $collID
						";
		$result_select = mysql_query($query_select) or die(mysql_error());

		# AFFICHAGE DE LA SELECTION
		#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE CE MEMBRE
		$rep_img = './img/consoles/';
		$i = 0;
		while($row_select = mysql_fetch_assoc($result_select)) {
			$i++;
			$consID = $row_select["consID"];
			$url = fp_makeURL('collections.php', $collID,$consID);
			$consNom = $row_select["consNom"];
			$editeur = $row_select["editNom"];
			$consDateSortie = substr($row_select['consDateSortie'],6,2)."/".substr($row_select['consDateSortie'],4,2)."/".substr($row_select['consDateSortie'],0,4);
			$image = $rep_img.$row_select["consID"].'.'.$row_select["consExtImg"];	
			
			# COUNT LE NOMBRE DE JEUX SUR CETTE CONSOLE
			$query_count="SELECT comporte.*,count(comporte.compIDJeu) as nbJeux 
						FROM collections,comporte 
						WHERE collections.collID = comporte.compIDColl 
						AND comporte.compIDColl = $collID
						AND comporte.compIDCons = $consID 
						GROUP BY comporte.compIDCons";
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
									<img src='$image' width='48px' height='48px'/><br>
								</td>
								<td width='200px'>
								Nom :<br>
								Date de sortie :<br>
								Fabricant :<br>
							</td>
							<td width='200px'>
								<b>$consNom</b><br>
								<b>$consDateSortie</b><br>
								<b>$editeur</b><br>
							</td>
						</tr>
						</table>
						</div>
						<div class='pied'>
						";
						if($nbJeux>0){
								echo "<a href='$url'>Voir ses <b>$nbJeux</b> jeux.</a>";
							}
							else{
								echo "Ce membre ne posséde aucun jeu.";
							}
            echo"   </div>
                </div>						
				";
		}
	}
	# LA LISTE DES JEUX SI ON A 2 PARAMETRES, CELUI DE LA COLLECTION ET CELUI DE LA CONSOLE CHOISIT
	elseif(count($Params) == 2){
		$collID = $Params[0];
		$consID = $Params[1];
		include('inc/visites_collection.php');
		// Ajout d'une visite pour cette collection
		include('inc/visites_collection.php');
		$query_select_pseudo_console ="SELECT membPseudo,consNom
								FROM membres,collections,appartient,consoles
								WHERE collections.collIDMemb = membres.membID
								AND appartient.appartIDColl = collections.collID
								AND consoles.consID = appartient.appartIDCons
								AND consID = $consID
								AND membID = $collID
								";
		$result_select_pseudo_console = mysql_query($query_select_pseudo_console) or die(mysql_error());
		$row_select_pseudo_console = mysql_fetch_assoc($result_select_pseudo_console);
		$consNom = $row_select_pseudo_console["consNom"];
		$membPseudo = $row_select_pseudo_console["membPseudo"];
		echo "
			<div id='main'>
                <div style='margin-top:20px;'>
                    <h1>La collection de $membPseudo sur $consNom</h1>
				</div>
		";		
		

	$query_select="	SELECT jeux.*,consoles.*,comporte.* FROM 
					collections,comporte,jeux,consoles
					WHERE collections.collID = comporte.compIDColl 
					AND comporte.compIDJeu = jeux.jeuID 
					AND consoles.consID = jeux.jeuIDCons
					AND collections.collIDMemb = $collID 
					AND comporte.compIDCons = $consID
					ORDER BY jeuNom ASC";
	$result_select = mysql_query($query_select) or die(mysql_error());	
	# AFFICHAGE DE LA SELECTION
	#ON AFFICHE LA LISTE DES CONSOLE QUE POSSEDE LE MEMBRE

	$rep_img = './img/jeux/';
	$i=0;
	while($row_select = mysql_fetch_assoc($result_select)) {
	$i++;
		$exemplaire = $row_select["nbExemplaire"];
		if($row_select["etat"] == 1 ){$etat ="Blister";}elseif ($row_select["etat"] == 2 ){$etat ="Comme neuf";}elseif ($row_select["etat"] == 3 ){$etat ="Bon état";}elseif ($row_select["etat"] == 4 ){$etat ="Moyen";}elseif ($row_select["etat"] == 5 ){$etat ="Mauvais";}else{$etat="-";}
		if($row_select["condition"] == 1 ){$condition ="Complet";}elseif ($row_select["condition"] == 2 ){$condition ="Boite";}elseif ($row_select["condition"] == 3 ){$condition ="Notice";}elseif ($row_select["condition"] == 4 ){$condition ="Loose";}else{$condition="-";}
		$jeuID = $row_select["jeuID"];
		$consID = $row_select["jeuIDCons"];
		$codeAffichage = $row_select["codeAffichage"];
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
								<td width='100px'>
									Exemplaire :<br>
									Etat :<br>
									Condition :<br>
								</td>
								<td width='100px'>
									<b>$exemplaire</b><br>
									<b>$etat</b><br>
									<b>$condition</b><br>
								</td>		
								<td width='200px'>
									<img src='$image' $type><br>
								</td>
								<td width='200px'>
									Nom :<br>
									Date de sortie :<br>
									Genre :<br>
									Editeur :<br>
									Développeur :
								</td>
								<td width='200px'>
									<b>$jeuNom</b><br>
									<b>$jeuDateSortie</b><br>
									<b>$jeuGenre</b><br>
									<b>$editeur</b><br>
									<b>$developpeur</b>
								</td>
							</tr>
						</table>
					</div>
					<div class='pied'>";
					if($nbJeux > 1)
					{echo "$nbJeux membres possédent ce jeu.";}
					else
					{echo "$nbJeux membre posséde ce jeu.";}
					echo "</div>
               </div>		
					
			";
	}
}
}
echo "</div>";
?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>