<div id="insco">
<!--
<div id="dewplayer">
<object type="application/x-shockwave-flash" data="./dewplayer/dewplayer-multi.swf" width="200" height="20" id="dewplayer" name="dewplayer">
<param name="movie" value="./dewplayer/dewplayer-multi.swf" />
<param name="flashvars" value="mp3=./dewplayer/mp3/test1.mp3|./dewplayer/mp3/test3.mp3|./dewplayer/mp3/test11.mp3|./dewplayer/mp3/test12.mp3|./dewplayer/mp3/test13.mp3|./dewplayer/mp3/test14.mp3&amp;autostart=1&amp;autoreplay=1&amp;showtime=1&amp;randomplay=1xml=playlist.xml&volume=30" />
<param name="wmode" value="transparent" />
</object>  
</div>-->
<?php
$start_time = microtime();
if(isset($_SESSION['login']) && isset($_SESSION['mdp'])){
	 connexion_bd();
	  //on va chercher tout ce qui correspond à l'utilisateur
	 $affiche = mysql_query("SELECT * FROM membres WHERE membPseudo='".mysql_real_escape_string(stripcslashes($_SESSION['login']))."' AND membActivation='".mysql_real_escape_string('1')."'");
	 $result = mysql_fetch_assoc($affiche);
	
	 //http://php.net/manual/fr/function.extract.php
	 extract($result);
	 //on libère le résultat de la mémoire
	 mysql_free_result($affiche);

	if (isset($_SESSION['login']) && $_SESSION['mdp'] && $result['membType'] == '2') 
        echo "<a href='https://www.facebook.com/retrogamerstock'>Facebook</a> | <a href='".$site_url."/admin_retro/retro_index.php'>Administration</a> | <a href='".$site_url."/monprofil.php'>Mon profil</a> | <a href='".$site_url."/inc/logout.php'>Déconnexion</a>"; 
	else if (!empty($_SESSION['login']) && $_SESSION['mdp'] )
        echo "<a href='https://www.facebook.com/retrogamerstock'>Facebook</a> | <a href='".$site_url."/monprofil.php'>Mon profil</a> | <a href='".$site_url."/inc/logout.php'>Déconnexion</a>";
}
else { echo '<a href="https://www.facebook.com/retrogamerstock">Facebook</a> | <a href="'.$site_url.'/connexion.php" title="Connexion">Connexion</a> | <a href="'.$site_url.'/inscription.php" title="Inscription">Inscription</a>';}
?>   
</div>
<div id="header">
<a title="<?php echo $nom_site ?>" href="<?php echo $site_url ?>">
    <div id="logo">
        <img alt="logo" src="<?php echo $site_url ?>/img/logo.png">
    </div>
</a> 
</div>