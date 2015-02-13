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
				$anID = fp_getURL($_GET["x"]); // récupération de l'id de l'article
				connexion_bd(); //connexion a la base de donnée
				$sql_mail_dest = "SELECT * FROM annonces,membres WHERE annonces.anAjouterPar = membres.membID AND annonces.anID = $anID";
                $result_mail_dest = mysql_query($sql_mail_dest); //la boucle pour lister le contenu de la table avec une limit de 10 news
                $row_mail_dest = mysql_fetch_assoc($result_mail_dest);
				$destinataire = $row_mail_dest["membMail"];
				$anTitre = $row_mail_dest["anTitre"];
				$url_annonce = fp_makeURL('annonces.php', $anID);
				?>
				<h1>
				Répondre à une annonce
				</h1><br/>
				<form method = "POST" action="<?php echo $url_annonce;?>">
        			<div class="inscri">
					<label>Pseudo / Nom *:</label> 
					<input type="text" value="<?php if (!empty($_POST["pseudo"])) { echo stripcslashes(htmlspecialchars($_POST["pseudo"],ENT_QUOTES)); } ?>" name="pseudo" size="25" class="inscription">
                    </div>
                    <div class="inscri">
					<label>E-mail *:</label> 
					<input type='text' value="<?php if (!empty($_POST["mail"])) { echo stripcslashes(htmlspecialchars($_POST["mail"],ENT_QUOTES)); } ?>" name="mail" size="25" class="inscription">
					</div>
                    <div class="inscri">
					<label>Message *:</label>
					<textarea rows="3" cols="30" name="message" class="inscription"> 
					<?php if (!empty($_POST["message"])) { echo stripcslashes(htmlspecialchars($_POST["message"],ENT_QUOTES)); } ?>
					</textarea>
					</div><br>
                    <br />
                    <div class="inscri">
            		<input type="submit" name="btnEnvoyer" value="Envoyer" class="boutton" style="width:193px">
                    </div>
                    <br />
                    <span style="color:#F00;font-weight:bold;">Tout les champs sont obligatoire<br />
					</span>           
				</form>
				<?php
				$textarea = stripcslashes(htmlspecialchars($_POST["message"],ENT_QUOTES));
				if(isset($_POST['btnEnvoyer']) && $_POST['btnEnvoyer']=='Envoyer'){
				//si pseudo vide
				if(empty($_POST['pseudo'])){
					echo '<div class="erreur">Entrez un pseudo / nom</div>';
				}
				//si l'email vide
				else if(empty($_POST['mail'])){
					echo '<div class="erreur">Entrez une adresse mail</div>';
				}
				//si message vide
				else if(empty($textarea)){
					echo '<div class="erreur">Entrez un message</div>';
				}
				else{
				// Format mail HTML
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				// En tete
				$headers .= 'From:'.$_POST["mail"]."\r\n";
				$headers .= 'Cc:' . "\r\n";

				$to      = $destinataire;
				$subject = 'RetrogamerStock : Reponse a votre annonce '.$anTitre;
				
				
				$messages .= "<html>
								<head>
									<title></title>
								</head>
								<body>
								<p>
								<h2>Reponse a votre annonce $anTitre</h2>
								Message de ".$_POST['pseudo'].
								"<br>"
								.$_POST['message']."
								</p>
								</body>
								</html>";
				mail($to, $subject, $messages,$headers);

                echo '<div class="ok"><center>Votre message à été envoyé avec succé, vous allez être redirigé...</center></div>
				<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",3000) </script>';
				}

                } 
}
else {  
				connexion_bd(); //connexion a la base de donnée
				$sql = "SELECT * FROM annonces,membres WHERE annonces.anAjouterPar = membres.membID AND annonces.anActivation = 1 ORDER BY anID DESC";
                $result = mysql_query($sql); //la boucle pour lister le contenu de la table avec une limit de 10 news
				echo "<div id='stat'>
                <div style='margin-top:20px;'>
                    <h1>Les annonces</h1>
                </div>
				<div style='margin-top:20px;text-align:right'>
					<a href='gerer_annonces.php'>Gérer vos annonces</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='ajouter_annonce.php'>Ajouter une annonce</a>
					
				";
				$rep_img = './img/membres/annonces/';
                while($row = mysql_fetch_assoc($result))
                 {
				 $image = $rep_img.$row['anID'].'.'.$row['anImg'];
				 $date = substr($row['anDate'],6,2)."/".substr($row['anDate'],4,2)."/".substr($row['anDate'],0,4);
				 $heure = $row['anHeure'];
                ?>
				
                <div id="news">
                    <div class="titre">
                    	<h2><?php echo $row['anTitre'] ?></h2>
                    </div>
                    <div class="corp">
						<table>
							<tr>
								<td width='200px'>
									<a class='fancybox' href='<?php echo $image;?>' title=''><img src='<?php echo $image;?>' alt='' width='100px' height='80px'/></a>
								</td>
								<td width='400px'>
									<?php echo $row['anTexte'] ?>
								</td>
							</tr>						
						</table>
                    </div>
                    <div class="pied">
						<a href="<?php echo fp_makeURL('annonces.php', $row["anID"]); ?>">Contacter le membre</a><br />
                        Par <?php echo $row['membPseudo'].' le '.$date.' à '.$heure?>
                    </div>
                </div>
                <?php } 
	 } ?>
            </div>
        <?php include('inc/footer.php'); ?>
        </div>
	</body>
</html>