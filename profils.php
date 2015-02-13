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
		<head>
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
			if(!empty($_GET["x"]))
			{

			$collID = fp_getURL($_GET["x"]);
			include('inc/visites_collection.php');
			$query_select = "select membres.*,
count(DISTINCT(appartient.appartIDCons)) as nbCons,
count(DISTINCT(comporte.compIDJeu)) as nbJeu from membres

	left join appartient	
	on membres.membID = appartient.appartIDColl
	left join comporte
	on membres.membID = comporte.compIDColl
	WHERE membres.membActivation = '1'
	AND membres.membID = '$collID'
	GROUP BY 1,2
	ORDER BY membres.membPseudo ASC";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$row_select = mysql_fetch_assoc($result_select);
	$membPseudo = $row_select["membPseudo"];
	echo "<h1>
				Profil de $membPseudo
			</h1>
			";

	$rep_image = "./img/membres/";
	
	if(!empty($row_select["membImg"]))
	{$image = $rep_image.$collID.".".$row_select["membImg"];}
	else
	{$image = $rep_image.'0.jpg';}
	$membMail = $row_select["membMail"];
	$nbConsoles = $row_select["nbCons"];
	$nbJeux = $row_select["nbJeu"];
	$nbVisites = $row_select["nbVisites"];
	$DateConnection = substr($row_select["membDateConnection"],6,2)."/".substr($row_select["membDateConnection"],4,2)."/".substr($row_select["membDateConnection"],0,4);
	
	$dateInscription = substr($row_select['membDateInscription'],6,2)."/".substr($row_select['membDateInscription'],4,2)."/".substr($row_select['membDateInscription'],0,4);

	echo "
			<div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
					<div class='corp'>
						<table>
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
								<td>
									<b>$membPseudo</b><br>
									<b>$dateInscription</b><br>
									<b>$nbConsoles</b><br>
									<b>$nbJeux</b><br>
									<b>$DateConnection</b><br>
								</td>
							</tr>
						</table>
					</div>
					 <div class='pied'>
					</div>
			</div>
             
		";
	# Ici on télécharge les photos de la collections du membre
	$query_select = "SELECT * FROM photos_collection WHERE phIDColl = '$collID'";
	$result_select = mysql_query($query_select) or die(mysql_error());
	$repertoire_image = "./img/membres/collections/";
	$i = 1;
	$num_rows = mysql_num_rows($result_select);
	# Si il a de photos on les affiche
	echo "<h1>
				Photos de sa collection
		</h1>
		<div id='news'>
                    <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'><table>";
	if($num_rows > 0)
	{
		
		while($row_select = mysql_fetch_assoc($result_select)) {
			$image = $repertoire_image.$collID."_".$row_select["phNumero"].".".$row_select["phExt"];
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
	
	}
	# sinon on affiche un message
	else
	{
	echo "Ce membre n'a actuellement pas de photos de sa collection.";
	}
	echo "</table></div>
                 <div class='pied'>
				</div>";
	
	echo "</div>
		

		 <div style='margin-top:20px;'>
				<h1>Jeux favoris</h1>
			</div>
			<div id='news'>
			         <div class='titre'>
                    	<h2>$membPseudo</h2>
                    </div>
                    <div class='corp'>";
		
			$rep_img = './img/jeux/';
		
			$sql_count = "SELECT count(*) as nbFavoris FROM comporte WHERE `compIDColl` = '$collID' AND `favori` = '1'";
			$result_count = mysql_query($sql_count) or die(mysql_error());
			$row_count = mysql_fetch_assoc($result_count);
			if($row_count["nbFavoris"] > 0)
			{
				$sql = "SELECT * FROM comporte,jeux,consoles WHERE comporte.compIDJeu = jeux.jeuID AND comporte.compIDCons = consoles.consID AND `compIDColl` = '$collID' AND `favori` = '1' ORDER BY compIDCons ASC";
				$result = mysql_query($sql) or die(mysql_error());
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
			}
			else
			{
				echo "Ce membre n'a pas séléctionné de jeux favoris.";
			}
echo "		
			</div>
			
";
	}	

	
			?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>