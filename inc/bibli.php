<?php
//_____________________________________________________________________________
//
//	BIBLIOTHEQUE DE FONCTIONS
//_____________________________________________________________________________

// La gestion de la session est faite uniquement avec un cookie
@ini_set('session.use_cookies', 1);
@ini_set('session.use_only_cookies', 1);

//_____________________________________________________________________________	
//
//				DEFINITION DE CONSTANTES
//
// Utiliser des constantes est particulièrement interressant avec PHP.
// En plus de fournir un moyen mnémotechnique pour stocker des données,
// les constantes sont "super-globales" : elles peuvent être utilisées
// dans les fonctions sans déclaration.
//_____________________________________________________________________________

define ('BD_ADRESSE', 'localhost');		// Adresse de la base de données
define ('BD_NOM', 'starblags');			// Nom de la base de données
define ('BD_USER', 'starblags_user');	// Nom utilisateur base de données
define ('BD_PASSE', 'starblags_pass');	// Mot de pase base de données

define ('MAIL_SENDER', 'francois.piat@univ-fcomte.fr');
define ('MAIL_SMTP', 'smtp.univ-fcomte.fr');
	
define ('ADRESSE_SITE', 'http://localhost/starblags/');
define ('ADRESSE_PAGE', ADRESSE_SITE.'php/');
define ('ADRESSE_RSS', ADRESSE_SITE.'rss/');

define('FP_DEBUG', TRUE);	// Indicateur pour affichage des messages d'erreur
							// Si TRUE, les messages sont affichés avec des
							// informations complètes.
							// Quand l'appli est testée et mise en exploitation
							// cette constante devrait être définie avec FALSE.

define('REP_UPLOAD', '../upload/');	// Nom du répertoire d'upload pour les images

define('RC4_CLE', 'françois');	// Clé pour le cryptage RC4;
define('RC4_SIGNE', 'piat');	// Signature des données cryptées

// Liens pour bandeau des pages
define('LIEN_AUCUN', 0);	// Pas de lien
define('LIEN_NA_LA', 1);	// Nouvel Article et Liste Article
define('LIEN_MB_NA', 2);	// Mon Blog et Nouvel Article
define('LIEN_MB_LA', 3);	// Mon Blog et Liste Article
define('LIEN_FORM', 4);		// Formulaire d'authentification
define('LIEN_MB_LA_NA', 5);	// Mon Blog et Liste Article et Nouvel Article

//set_magic_quotes_runtime(0);	// On désactive la protection automatique des caractères
	ini_set ("magic_quotes_runtime", 0);
// On définit la méthode de protection des zones passées à la base
// dans les réquêtes. On le fait ici une fois pour toutes, plutôt
// qu'à chaque appel de la fonction de protection.
if (function_exists('mysql_real_escape_string')) {
	define('FP_PROTECT', 'mysql_real_escape_string');
} elseif (function_exists('mysql_escape_string')) {
	define('FP_PROTECT', 'mysql_escape_string');
} else if (get_magic_quotes_gpc() == 0) {
	define('FP_PROTECT', 'addslashes');
}

// On définit PAGE_INDEX si il n'est pas déjà défini.
if (!defined('PAGE_INDEX')) {
	define('PAGE_INDEX', false);
}
//_____________________________________________________________________________
//
//				BASE DE DONNEES
//_____________________________________________________________________________
/**
 * Connexion et selection de la base de données
 * 
 * @global	const	BD_ADRESSE
 * @global	const	BD_USER
 * @global	const	BD_PASSE
 * @global	const	BD_NOM
 */
function fp_bdOpen() {
	@mysql_connect(BD_ADRESSE, BD_USER, BD_PASSE)
		or exit('Pas de connexion au serveur');
	@mysql_select_db(BD_NOM)
		or exit('Pas de base de données');
}
//_____________________________________________________________________________
/**
 * Affichage d'un message en cas d'erreur sur la base de données.
 * La sortie bufférisée est effacée et un message est affiché,
 * avec plus ou moins d'informations suivant la constante FP_DEBUG.
 * Le traitement est totalement arrêté (exit()).
 * 
 * @param	string	$Sql	Texte de la requête SQL provoquant l'erreur
 * @param	string	$Page	Nom de la page PHP provoquant l'erreur
 * @param	integer	$Ligne	Numéro de la ligne provoquant l'erreur
 * @global	const	FP_DEBUG	Indicateur de type du message
 */
function fp_bdErr($Sql, $Page, $Ligne) {
	ob_end_clean();  // Effacement de la sortie déjà bufférisée
	
	echo '<html><head><title>Erreur application</title></head><body>';
	if (FP_DEBUG) {
		// Informations longues, à utiliser lors du développement du site
		// pour avoir un maximum d'info sur les erreurs.
		echo'<table align="center" border=1 cellspacing="0" width="80%" cellpadding="4">',
			'<tr><td colspan="2" bgcolor="#FF0000"><p align="center"><b>',
			'Il semble que nous ayons un problème ! </b></td></tr>',
			'<tr><td><p>Fichier</td><td><p>',
			$Page,
			'</td></tr><tr><td><p>Ligne</td><td><p>',
			$Ligne,
			'</td></tr><tr><td><p>Erreur</td><td><p>',
			mysql_errno(),
			'</td></tr><tr><td><p>mySQL</td><td><p>',
			mysql_error(),
			'</td></tr><tr><td><p>Requête</td><td><p>',
			$Sql,
			'</td></tr></table>';
	} else {
		// Informations courtes, à utiliser quand le site est sur le web.
		// On pourrait encore réduire l'affichage des informations et doubler
		// avec un fichier d'erreurs sur le serveur et/ou l'envoi d'un mail
		// au webmaster ou au développeur.
		echo '<table align="center" border=1 cellspacing="0" width="80%" cellpadding="4">',
			'<tr><td colspan="2" bgcolor="#FF0000"><p align="center"><b>',
			'Il semble que nous ayons un problème ! </b></td></tr>',
			'<tr><td><p>Fichier</td><td><p>',
			basename($Page),
			'</td></tr><tr><td><p>Ligne</td><td><p>',
			$Ligne,
			'</td></tr><tr><td><p>Erreur</td><td><p>',
			mysql_errno(),
			'</td></tr></table>';
	}
	echo '</body></html>';
	exit();  // Arrêt total du traitement
}

//_____________________________________________________________________________
//
//				GESTION DE CODE HTML
//_____________________________________________________________________________
/**
 * Les 3 fonctions suivantes (fp_modeleTraite, fp_modeleGet et fp_modeleAffiche)
 * gérent les traitements des modèles. On éclate la gestion en 3 fonctions pour
 * plus de modularité du code, et pour une évolution plus facile si le principe
 * des modèles était revu.
 */
//_____________________________________________________________________________
/**
 * Lecture d'un fichier modele, remplacement des motifs et affichage
 * 
 * @param string	$Fichier	Nom du fichier à lire (sans répertoire ni extension)
 * @param array		$Remplace	Tableau associatif des remplacements
 * @return string	Contenu du fichier
 */
function fp_modeleTraite($Fichier, $Remplace) {
	$Modele = fp_modeleGet($Fichier);	// Récupération du modele
	fp_modeleAffiche($Modele, $Remplace);		
}
//_____________________________________________________________________________
/**
 * Lecture d'un fichier modele
 * 
 * @param string	$Fichier	Nom du fichier à lire (sans répertoire ni extension)
 * @return string	Contenu du fichier
 */
function fp_modeleGet($Fichier) {
	// Suivant la page en cours, le chemin d'accès au fichier modéle est différent:
	// - la page index.php doit référencer modeles/$Fichier
	// - les autres pages doivent référencer ../modeles/$Fichier
	// Pour faire cette différence on utilise $_SERVER['PHP_SELF'] qui contient
	// le chemin d'accès et le nom du fichier en cours d'éxécution (par exemple
	// /starblags/index.php ou /starblags/php/articles_voir.php ou ...
	// On ne pourrait pas utiliser __FILE__ qui contient le nom du fichier dans
	// lequel se situe l'instruction (ici bibli.php).
	if (basename($_SERVER['PHP_SELF']) == 'index.php') {
		$Fichier = "modele/$Fichier.html";
	} else {
		$Fichier = "../modele/$Fichier.html";
	}
	$Pointeur = @fopen($Fichier, 'r');	// ouverture du fichier
	// Traitement si erreur
	if ($Pointeur === false) {
		ob_end_clean();  // Effacement de la sortie déjà bufférisée
		echo '<html><head><title>Erreur application</title></head><body>',
			'Le fichier ', $Fichier, ' ne peut pas être ouvert.',
			'</body></html>';
		exit();  // Arrêt total du traitement
	}
	$Buffer = fread($Pointeur, filesize($Fichier));	// Lecture
	fclose($Pointeur);	// fermeture du fichier
	return $Buffer;
}
//_____________________________________________________________________________
/**
 * Remplacement des motifs d'un modele et affichage
 * 
 * @param string	$Modele		Texte du modéle
 * @param array		$Remplace	Tableau associatif des remplacements
 */
function fp_modeleAffiche($Modele, $Remplace) {
	// Comme la fonction str_replace accepte des tableaux, pour faire 
	// les remplacements dans les modèles on utilise un tableau associatif : 
	// - la clé est l'expression à remplacer 
	// - la valeur est la valeur de remplacement
	// De cette façon la fonction ne sera appelée qu'une fois par modele.
	// On utilisera la fonction array_keys($Remplace) pour récupèrer un tableau
	// des clés et array_values($Remplace) pour récupèrer un tableau des valeurs.

	echo str_replace(array_keys($Remplace), array_values($Remplace), $Modele);
	
	// Il peut sembler "bizarre" d'avoir une fonction avec cette seule ligne.
	// L'explication tient dans la facilité qu'on aurra plus tard si on a
	// à changer le traitement. Les appels à la fonction devraient rester les
	// mêmes, seule le code de la fonction serait à changer.
}

//_____________________________________________________________________________
/**
 * Renvoie le code html des liens de mise à jour pour le bandeau des pages privées
 * 
 * @param	integer	$Type		Type des liens à générer. Voir constantes LIEN_xxx
 * @return	string	Code HTML des liens
 */
function fp_htmlBandeau($Type) {
	$Liens = array(	'NA' => '<a href="'.fp_makeURL('article_maj.php', 0).'">Nouvel article</a>',
					'LA' => '<a href="articles_liste.php">Mes articles</a>',
					'MB' => '<a href="blog_maj.php">Mon blog</a>');
	$BlocLien = '';
	if ($Type == LIEN_MB_LA_NA) {
		return $Liens['MB'].
				'<br>'.$Liens['LA'].
				'<div style="padding-top: 8px">' 
				.$Liens['NA'].
				'</div>';
	}
	
	if ($Type == LIEN_NA_LA) {
		return "{$Liens['NA']}<br>{$Liens['LA']}";
	}
	
	if ($Type == LIEN_MB_NA) {
		return "{$Liens['MB']}<br>{$Liens['NA']}";
	}
	
	if ($Type == LIEN_MB_LA) {
		return "{$Liens['MB']}<br>{$Liens['LA']}";
	}
	return '';
}
//_____________________________________________________________________________
/**
 * Affiche le code html d'une ligne de boutons de formulaire.
 * 
 * Cette fonction accepte un nombre variable de paramètres.
 * Seul le premier est défini dans la définition de la fonction.
 * Les paramètres suivants définissent les boutons.
 * La définition d'un bouton se fait dans une zone alpha de la forme :
 *  Type|Nom|Valeur|JavaScript
 * 	Type	type du bouton
 * 		S : submit
 * 		R : reset
 * 		B : button
 * 	Nom		nom du bouton  (attribut name)
 * 	Valeur	valeur du bouton (attribut value)
 * 	JavaScript	fonction JavaScript pour événément onclick
 * 
 * Exemple : fp_htmlBoutons(2, 'B|btnRetour|Liste des sujets|history.back()', 'S|btnValider|Valider'
 * 
 * @param	integer	$Colspan	Nombre de colonnes de tableau à joindre. Si -1 pas dans un tableau
 * @param	string	Indéfini	Définition d'un bouton. Il peut y avoir
 * 								autant de définitions que désiré.
 */
function fp_htmlBoutons($Colspan) {
	if ($Colspan == -1) {
		echo '<p align="right">';
	} else {
		echo '<tr>',
				'<td colspan="', $Colspan, '">&nbsp;</td>',
			'</tr>',
			'<tr>',
				'<td colspan="'.$Colspan.'" align="right">';
	}
	
	for ($i = 1, $NbArg = func_num_args(); $i < $NbArg; $i++) {
		$Bouton = func_get_arg($i);
		$Description = explode('|', $Bouton);

		if ($Description[0] == 'S') {
			$Description[0] = 'submit';
		} elseif ($Description[0] == 'R') {
			$Description[0] = 'reset';
		} elseif ($Description[0] == 'B') {
			$Description[0] = 'button';
		} else {
			continue;
		}
		
		if (!isset($Description[3])) {
			$Description[3] = '';
		}
		
		echo '&nbsp;&nbsp;',
				'<input type="', $Description[0], '" ',
				'name="', $Description[1], '" ',
				'value="', $Description[2], '" ',
				'class="bouton" ',
				( ($Description[3] == '') ? '>' : 'onclick="'.$Description[3].'">');
	}
	
	echo ($Colspan == -1) ? '</p>' : '</td></tr>';
}
//_____________________________________________________________________________
/**
 * Affiche le code html d'une ligne de tableau écran de saisie.
 * 
 * Le code html généré est de la forme
 * <tr><td> libelle </td><td> zone de saisie </td></tr>
 * 
 * Seuls les 3 premiers paramètres sont obligatoires. Les autres dépendent
 * du type de la zone.
 * Le libellé de la zone est protégé pour un affichage HTML
 * Si la valeur de la zone est du texte, il est protégé pour un affichage HTML
 * 
 * @param	string	$Type	type de la zone
 * 							A : textarea
 * 							AN : textarea uniquement en affichage
 * 							C : case à cocher
 * 							H : hidden
 * 							P : password
 * 							R : bouton radio
 * 							S : select (liste)
 * 							T : text
 * 							TN : text  uniquement en affichage
 * @param	string	$Nom	nom de la zone (attribut name)
 * @param	mixed	$Valeur	valeur de la zone (attribut value)
 * 							Pour le type S, c'est l'élément sélectionné
 * @param	string	$Lib	libellé de la zone
 * @param	integer	$Size	si type T ou P : longueur (attribut size)
 * 							si type A : longeur (attribut cols)
 * 							si type S : nombre de lignes affichées (attribut size)
 * 							si type R : 1 = boutons côte à côte / 2 = boutons superposés 
 * 							si type C : 1 = cases côte à côte / 2 = cases superposés 
 * @param	mixed	$Max	si type T ou P : longueur maximum (attribut maxlength)
 * 							si type A : nombre de ligne (attribut rows)
 * 							si type R : tableau des boutons radios (valeur => libellé)
 * 							si type C : tableau des case à cocher (valeur => libellé)
 * 							si type S : tableau des lignes de la liste (valeur => libellé)
 * @param	string	$Plus	Supplément (ex : fonction JavaScript gestionnaire d'événement)
 */
function fp_htmlSaisie($Type, $Nom, $Valeur, $Lib = '', $Size = 80, $Max = 255, $Plus = '') {
	if (is_string($Valeur) && $Valeur != '') {
		$Valeur = fp_protectHTML($Valeur);
	}
	
	// Zone de type Hidden
	if ($Type == 'H') {
		echo '<input type="hidden" name="', $Nom, '" value="', $Valeur, '">';
		return;
	}

	$Lib = fp_protectHTML($Lib);
	
	switch ($Type) {
	//--------------- Zone de type Texte
	case 'T':
	case 'TN':
		echo '<tr>',
				'<td align="right">', $Lib, '&nbsp;</td>',
				'<td>',
					'<input type="text" name="', $Nom, '" ', $Plus, 
					'size="', $Size, '" maxlength="', $Max, '" value="', $Valeur, '" ',
					(($Type == 'T') ? 'class="saisie">' : 'class="saisie_non" readonly>'),
				'</td>',
			'</tr>';
		return;
		
	//--------------- Zone de type Textarea
	case 'A':
	case 'AN':
		echo '<tr>',
				'<td align="right" valign="top">', $Lib, '&nbsp;</td>',
				'<td>',
					'<textarea name="', $Nom, '" cols="', $Size, '" rows="'.$Max.'" ', $Plus,
					(($Type == 'A') ? 'class="saisie">' : 'class="saisie_non" readonly>'),
					$Valeur, '</textarea>',
				'</td>',
			'</tr>';
		return;		

	//--------------- Zone de type Password
	case 'P':
		echo '<tr>',
				'<td align="right">', $Lib, '&nbsp;</td>',
				'<td>',
					'<input type="password" name="', $Nom, '" ', $Plus, 
					'size="', $Size, '" maxlength="', $Max, '" value="', $Valeur, '" ',
					'class="saisie">',
				'</td>',
			'</tr>';
		return;

	//--------------- Zone de type bouton radio
	//--------------- Zone de type case à cocher
	case 'R':
	case 'C':		
		if ($Type == 'R') {
			$TypeAttr = 'radio';
			$NameAttr = $Nom;		
		} else {
			$TypeAttr = 'checkbox';
			$NameAttr = $Nom.'[]';
		}
		
		echo '<tr>',
				'<td align="right" ', (($Size == 2) ? 'valign="top">' : '>'),
					$Lib, '&nbsp;',
				'</td>',
				'<td>';
			
		$Nb = 0;
		foreach ($Max as $Val => $Txt) {
			if ($Size == 2) {
				$Nb ++;
				if ($Nb > 1) {
					echo '<br>';	
				}
			}
			echo '<input type="', $TypeAttr, '" name="', $NameAttr, '" value="', $Val, '"',
				( ($Valeur == $Val) ? ' checked="true">' : '>' ),
				fp_protectHTML($Txt), '&nbsp;&nbsp;&nbsp;';	
		}
		echo '</td>',
			'</tr>';
		return;

	//--------------- Zone de type Select (liste)
	case 'S':
		echo '<tr>',
				'<td align="right"', ( ($Size > 1) ? ' valign="top">' : '>'),
					$Lib, '&nbsp;',
				'</td>',
				'<td>',
					'<select name="', $Nom, '" size="', $Size, '" ', $Plus, ' class="saisie">';
			
		foreach($Max as $Cle => $Val) {
			echo '<option value="', $Cle, '"', ( ($Cle == $Valeur) ? ' selected="yes">' : '>' ), 
					$Val, 
				'</option>';	
		}
		
		echo 		'</select>',
				'</td>',
			'</tr>';
		return;
	}		
}

//_____________________________________________________________________________
/**
 * Affichage des messages d'erreur d'un formulaire
 * 
 * @param	array	$Erreurs	Tableau associatif des erreurs
 */
function fp_htmlErreurs($Erreurs) {
	echo '<div id="blcErreurs">';
	if (count($Erreurs) == 1) {
		echo 'L\'erreur suivante a été détectée ';
	} else {
		echo 'Les erreurs suivantes ont été détectées ';
	}
	echo 'dans le formulaire de saisie :';
	
	foreach($Erreurs as $Texte) {
		echo '<p class="erreurTexte">', 
				fp_protectHTML($Texte), 
			'</p>';
	}
	
	echo '</div>';
}
//_____________________________________________________________________________
//
//				FONCTIONS DIVERSES
//_____________________________________________________________________________
/**
 * Protection d'une chaîne de caractères pour une utilisation SQL
 * 
 * @param	string	$Texte		Texte à protéger
 * @global	string	FP_PROTECT	Nom de la fonction de protection à utiliser 
 * 
 * @return	string	Chaîne protégée
 */
function fp_protectSQL ($Texte) {
	if (get_magic_quotes_gpc() == 1) {
		$Texte = stripslashes($Texte);
	}
	// On est obligé de passer par une variable car on ne peut pas faire de 
	// "constantes fonctions" comme on peut faire des "variables fonctions".
	$Fonction = FP_PROTECT;
	return $Fonction($Texte);
}
//_____________________________________________________________________________
/**
 * Protection d'une chaîne de caractères pour un affichage HTML
 * 
 * @param	string	$Texte	Texte à protéger
 * @param	boolean	$BR		TRUE si remplacement des saut de ligne par le tag <br>
 * 
 * @return	string	Code HTML généré
 */
function fp_protectHTML($Texte, $BR = FALSE) {
	return ($BR) ? nl2br(htmlspecialchars($Texte)) : htmlspecialchars($Texte);
}
//_____________________________________________________________________________
/**
 * Enléve la protection automatique de caractères (magic_quotes_gpc) sur $_POST
 * 
 * Si la variable de configuration PHP magic_quotes_gpc a la valeur 1
 * certains caractères des zones de formulaires sont automatiquement protégés.
 * Celà peut poser problème quand on veut par exemple réafficher les infos
 * saisies. Cette fonction permet d'enlever les caractères de protection
 * trouvés dans les léléments du tableau $_POST.
 * 
 * @global	array	$_POST	Les éléments du formulaire
 */
function fp_stripPOST() {
	if (get_magic_quotes_gpc() == 0) {
		return;
	}
	foreach($_POST as $Cle => $Zone) {
		$_POST[$Cle] = stripslashes($Zone);
	}
}
//_____________________________________________________________________________
/**
 * Cryptage / decryptage d'une chaîne avec l'algorithme RC4
 *
 * @param	string	$Texte	Données à crypter
 * @param	string	$Cle	Clé de cryptage
 * 
 * @return string	Données cryptées ou décryptées
 */

 
 function fp_RC4($Texte, $Cle) {
    $Cles = array();	// tableau initialisé avec les octets de la clé
    $Etats = array();	// table d'états : flux appliqué sur le texte clair
    $Tmp = '';
    $CleLong = strlen($Cle);
	$TexteLong = strlen($Texte);
	$RC4 = '';
	
	// Première étape : création de 2 tableaux de 256 octets en fonction de la clé
	// Le tableau $Cles est initialisé avec les octets de la clé
	// Le tableau $Etats est initialisé avec les nombres de 0 à 255 permutés 
	// pseudo-aléatoirement selon le tableau K.
    for ($i = 0; $i <= 255; $i++) {
        $Cles[$i] = ord(substr($Cle, ($i % $CleLong), 1));
        $Etats[$i] = $i;
    }

    for ($i = $x = 0; $i <= 255; $i++) {
        $x = ($x + $Etats[$i] + $Cles[$i]) % 256;
        $Tmp = $Etats[$i];
        $Etats[$i] = $Etats[$x];
        $Etats[$x] = $Tmp;
    }
    
    // Deuxième étape : permutations pour le chiffrement/déchiffrement. 
    // Toutes les additions sont exécutées modulo 256.
	// Le tableau $Etats change à chaque itération en ayant deux éléments permutés.
    for ($a = $i = $j = 0, $k = ''; $i < $TexteLong; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $Etats[$a]) % 256;
        $Tmp = $Etats[$a];
        $Etats[$a] = $Etats[$j];
        $Etats[$j] = $Tmp;
        $k = $Etats[(($Etats[$a] + $Etats[$j]) % 256)];
        $Tmp = ord(substr($Texte, $i, 1)) ^ $k;
        $RC4 .= chr($Tmp);
    }

    return $RC4;
}
//_____________________________________________________________________________
/***
* Composition d'une URL avec cryptage des paramètres
*
* Les paramètres de l'URL sont mis les uns à la suite des autres, séparés par
* le caractère | (pipe). On ajoute en début de la chaîne des paramètres la
* signature de cryptage. La chaîne est ensuite protégée pour les caractères 
* spéciaux d'URL. Elle est ajoutée à l'URL avec comme nom x. On obtient ainsi
* par exemple : mapage.php?x=HKVSkS6t
* 
* @param	string 	$Url		Début de l'url (ex : mapage.php)
* @param	mixed 	$X		Paramètres de l'url. Ils sont d'un nombre indéterminé
* @global	string	RC4_SIGNE	Signature de cryptage
* @global	string	RC4_CLE		Cle de cryptage
* 
* @return string	URL cryptée
*/

function fp_crypte($X){
	$Params = RC4_SIGNE;
	$Args = func_get_args();
	$Params = fp_RC4($Params, RC4_CLE);
	return base64_encode($Params);
}


function fp_makeURL($Url, $X) {
	$Params = RC4_SIGNE;
	$Args = func_get_args();
	for($i = 1, $iMax = count($Args); $i < $iMax; $i ++) {
		$Params .= '|'.$Args[$i];
	}
	$Params = fp_RC4($Params, RC4_CLE);
	
	return $Url.'?x='.rawurlencode(base64_encode($Params));
	
}
//_____________________________________________________________________________
/**
 * Décryptage d'un paramètre GET et renvoi des valeurs contenues
 * 
 * Cette fonction est en quelque sorte l'inverse de de fp_makeURL.
 * Elle récupère la vairable $_GET['x'], la décrypte, vérifie la signature
 * puis renvoie les différentes valeurs trouvées sous la forme d'un tableau.
 * Le script est arrêté si
 * - le paramètre x est absent
 * - la signature n'est pas bonne
 * - il n'y a pas plus de une valeur
 * 
 * @global	array	$_GET['x']	Paramètre de la page
 * 
 * @return	mixed	Si plusieurs valeurs renvoie un tableau, sinon un scalaire
 */
 
 function fp_decrypte($Y){
	//$Params = base64_decode(rawurldecode($Y);
	$Params = base64_decode($Y);
	$Params = fp_RC4($Params, RC4_CLE);
	
	return $Params;
}

function fp_getURL() {
	if (!isset($_GET['x'])) {
		exit((FP_DEBUG) ? 'Erreur GET - '.__LINE__ : '');
	}

	$Params = base64_decode(rawurldecode($_GET['x']));
	$Params = fp_RC4($Params, RC4_CLE);
	$Params = explode('|', $Params);

	if (count($Params) < 2) {
		exit((FP_DEBUG) ? 'Erreur GET - '.__LINE__ : '');
	}
	if ($Params[0] != RC4_SIGNE) {
		exit((FP_DEBUG) ? 'Erreur GET - '.__LINE__ : '');
	}
		
	array_shift($Params);

	// Si plusieurs valeurs on renvoie un tableau avec les valeurs
	if (count($Params) > 1) {
		return $Params;
	}
	// Si une seule valeur on renvoie cette valeur uniquement
	return $Params[0];
}
//_____________________________________________________________________________
/***
* Retourne une date jma en amj
*
* @param string 	$Date 	format jj/mm/aaaa ou jj-mm-aaaa 
* 							ou jj.mm.aaaa ou jj mm aaaa ou jjmmaaaa
*
* @return integer format aaaammjj
*/
function fp_jmaAmj($Date) {
	if (strpos($Date , '/') !== FALSE) {
		return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/","\\3\\2\\1",$Date);
	}
	if (strpos($Date , '-') !== FALSE) {
		return preg_replace("/(\d{2})\-(\d{2})\-(\d{4})/","\\3\\2\\1",$Date);
	}
	if (strpos($Date , '.') !== FALSE) {
		return preg_replace("/(\d{2})\.(\d{2})\.(\d{4})/","\\3\\2\\1",$Date);
	}
	if (strpos($Date , ' ') !== FALSE) {
		return preg_replace("/(\d{2}) (\d{2}) (\d{4})/","\\3\\2\\1",$Date);
	}
	return preg_replace("/(\d{2})(\d{2})(\d{4})/","\\3\\2\\1",$Date);
}
//_____________________________________________________________________________
/***
* Retourne une date amj en j/m/a
* 
* @param integer 	$Date 	format aaaammjj
* 
* @return string chaîne jj/mm/aaaa
*/
function fp_amjJma($Date) {
	if ($Date == 0) {
		return '';
	}
	return preg_replace("/(\d{4})(\d{2})(\d{2})/","\\3/\\2/\\1",$Date);
}
//_____________________________________________________________________________
/**
* Vérification de la session d'un utilisateur.
* 
* A utiliser dans les pages de mise à jour pour s'assurer qu'une session
* est bien initialisée pour l'utiliseur.
* Si ce n'est pas le cas, l'utilisateur est redirigé sur la page d'acceuil.
* 
* @global	array	$_SESSION	variables de ssession
*/
function fp_verifSession() {
	$Ok = TRUE;
	if (!isset($_SESSION['IDBlog']) || !is_numeric($_SESSION['IDBlog'])) {
		$Ok = FALSE;
	}
	if (!isset($_SESSION['IDArticle']) || !is_numeric($_SESSION['IDArticle'])) {
		$Ok = FALSE;
	}
	if (!isset($_SESSION['UploadFrom'])) {
		$Ok = FALSE;
	}
	if (!isset($_SESSION['UploadNum']) || !is_numeric($_SESSION['UploadNum'])) {
		$Ok = FALSE;
	}
	
	if (!$Ok) {
		session_destroy();
		$_SESSION = array();
		header('Location: ../index.php');
		exit();  // fin PHP
	}
}
//_____________________________________________________________________________
/**
 * Envoi d'un mail au format HTML.
 * 
 * @param	string	$Destinataire	Adresse mail du destinataire
 * @param	string	$Objet			Objet du mail
 * @param	string	$Texte			Texte du mail 
 */
function fp_mail($Destinataire, $Objet, $Texte) {
	@ini_set('SMTP', MAIL_SMTP);
	@ini_set('sendmail_from', MAIL_SENDER);
	
	$FinLigne = "\r\n";
	
	// Composition de l'entête du mail
	$EnTete = 'From: "StarBlags" <'.MAIL_SENDER.'>'.$FinLigne
			.'Reply-To: '.MAIL_SENDER.$FinLigne
			.'MIME-Version: 1.0'.$FinLigne
			.'Content-Type: text/html; charset=iso-8859-1'.$FinLigne
			.'X-Priority: 1'.$FinLigne
			.'X-Mailer: PHP / '.phpversion().$FinLigne;
			
	// Envoi du mail
	if (!mail($Destinataire, $Objet, $Texte, $EnTete)) {
		$Msg = __LINE__.' - '.basename(__FILE__)
			.' - Un mail n\'a pas pu être envoyé.<br>'
			.'<a href="mailto:'.MAIL_SENDER.'">'
			.'Prévenez l\'administrateur du site.</a>';
		exit($Msg);
	}			
}
//_____________________________________________________________________________
/**
 * Récupèration de l'adresse IP du visiteur
 * 
 * @return	string	Adresse Ip du visiteur ou '' si impossible à déterminer
 */
function fp_getIP() {
    $IP = '';
    $Proxys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                    'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                    'HTTP_VIA', 'HTTP_X_COMING_FROM',
                    'HTTP_COMING_FROM', 'REMOTE_ADDR');
  
    foreach($Proxys as $Prox) {
        if (isset($_SERVER[$Prox])) {
            $IP = $_SERVER[$Prox];
            break;
        }
    }

    $Ok = preg_match('/^[0-9]{1,3}(.[0-9]{1,3}){3,3}/', $IP, $Exps = array());

    if($Ok && (count($Exps) > 0)) {
    	return $Exps[0];
    }
   
    return '';
}
//_____________________________________________________________________________
/**
 * Affichage du contenu d'un article
 * 
 * Si il n'y a pas d'images liées, le texte est simplement
 * affiché à la suite de l'entête.
 * Si il y a des images liées :
 * - on utilise un bloc pour les images du haut
 * - on utilise un bloc pour les images du bas
 * - pour les images de gauche, de droite et pour le texte
 * on utilise un tableau. C'est le plus simple pour éviter
 * des "bidouilles" pour l'alignement vertical des images.
 *
 *	 ____________________________________________________
 *  | bloc entête                                        |
 *  |____________________________________________________|
 *  | bloc image haut (si nécessaire)                    |
 *  |____________________________________________________|
 *   ____________________________________________________
 *  | cellule |  texte                         | cellule |
 *  | images  |                                | images  |
 *  | gauche  |                                | droite  |
 *  | ________|________________________________|_________|
 *   ____________________________________________________
 *  | bloc image bas (si nécessaire)                     |
 *  |____________________________________________________|	
 *   ____________________________________________________
 *  | bloc liens commentaire, note, etc.                 |
 *  |____________________________________________________|	
 * 
 *
 * @param	array	$Articles	Enregistrement table articles
 * @global	const	REP_UPLOAD	Répertoire de téléchargement des images
 */
function fp_articleAffContenu($Articles, $Modele) {
	// Recherche de la note de l'article
	$Sql = "SELECT sum(anNote)
			FROM articles_notes
			WHERE anIDArticle = {$Articles['arID']}";
			
	$R = mysql_query($Sql) or fp_bdErr($Sql, __FILE__, __LINE__);  // Exécution requête
	
	$BD = mysql_fetch_array($R);	// Récupération de la sélection
	$Note = $BD[0];
	mysql_free_result($R);
		
	//---------------------------------------------------------------
	// Traitement des images liées
	// Les tags des cellules images et  légendes sont stockés 
	// dans des matrices PHP qui serviront à construire les tableaux HTML.
	// Matrice : $Images[place][incrément]
	// (Rapel place : 0 = haut, 1 = droite, 2 = bas, 3 = gauche)
	$Images = $Illus = array();	
	
	$Remplace = array();	
	$Remplace['@_PHOTO_0_@'] = $Remplace['@_PHOTO_1_@'] = '';
	$Remplace['@_PHOTO_2_@'] = $Remplace['@_PHOTO_3_@'] = '';
		
	// Recherche des images liées à l'article
	$Sql = "SELECT *
			FROM photos
			WHERE phIDArticle = {$Articles['arID']}
			ORDER BY phNumero";
			
	$R = mysql_query($Sql) or fp_bdErr($Sql, __FILE__, __LINE__);  // Exécution requête

	while ($BD = mysql_fetch_assoc($R)) {  // Boucle de lecture de la sélection
		$Url = REP_UPLOAD."{$Articles['arID']}_{$BD['phNumero']}.{$BD['phExt']}";
		$Images[$BD['phPlace']][] = '<img src="'.$Url.'"><br>'
									.fp_protectHTML($BD['phLegende']);
	}
	mysql_free_result($R);
	
	for ($i = 0; $i < 4; $i ++) {
		$jMax = (isset($Images[$i])) ? count($Images[$i]) : 0;

		for ($j = 0; $j < $jMax; $j++) {
			$Remplace["@_PHOTO_{$i}_@"] .= $Images[$i][$j];
		}
	}		
 				
	//---------------------------------------------------------------
	// Affichage du contenu de l'article
	$Remplace['@_DATE_@'] = fp_amjJma($Articles['arDate'])." - {$Articles['arHeure']}";	 
 	// En-tête avec le titre de l'article et sa date de parution
 	// Si l'utilisateur qui affiche la page est le créateur du blog
	// il peut modifier un article en cliquant sur son titre.
	$Titre = fp_protectHTML($Articles['arTitre']);		
	if (isset($_SESSION['IDBlog']) && $_SESSION['IDBlog'] == $IDBlog) {
		// Les paramètres du lien sont cryptés (IDarticle)
		$Url = fp_makeURL('article_maj.php', $Articles['arID']);
		$Titre = '<a href="'.$Url.'">'.$Titre.'</a>';	 	
	}
	$Remplace['@_TITRE_@'] = $Titre;
	$Remplace['@_TEXTE_@'] = $Articles['arTexte'];
						
	//---------------------------------------------------------------
	// Affiche des liens - fin d'un article.
	// - le nombre de commentaires et lien pour en ajouter, 
	// - la note éventuelle, 
	// - le lien pour noter.
	$Liens = '';
	
	// Si il y a des commentaires pour l'article, on affiche un lien
	// pour l'affichage d'une fenêtre popup avec les commentaires
	if ($Articles['NbComments'] > 0) {
		// Les paramètres du lien sont cryptés (IDArticle)
		$Url = fp_makeURL('comments_voir.php',$Articles['arID']);
		$Url = "javascript:FP.ouvrePopUp('$Url')";
		
		$Liens .= '<a href="'.$Url.'" class="articleLienCom">'
				.$Articles['NbComments']
				.( ($Articles['NbComments'] == 1) ? ' commentaire</a>':' commentaires</a>');
	}	
	
	// Lien pour la saisie d'un commentaire
	if ($Articles['arComment'] == 1) {
		// Les paramètres du lien sont cryptés (IDArticle)
		$Url = fp_makeURL('comment_ajout.php', $Articles['arID']);
		$Url = "javascript:FP.ouvrePopUp('$Url')";
		
		$Liens .= '<a href="'.$Url.'" class="articleLienComAjout">ajouter un commentaire</a>';
	}	

    // Note de l'article
	if ($Note > 0) {
		$Liens .= '<a class="articleNote">'.$Note.'</a>'; 
	}

	// Lien pour noter l'article et fin du tableau
	// Les paramètres du lien sont cryptés (IDArticle)
	$Url = fp_makeURL('article_noter.php', $Articles['arID']);
	$Url = "javascript:FP.ouvrePopUp('$Url')";
	
	$Liens .= '<a href="'.$Url.'" class="articleLienNoteAjout">noter</a>';

	$Remplace['@_LIENS_@'] = $Liens;
	
	fp_modeleAffiche($Modele, $Remplace);	// Remplacement et affichage du modele		
}
?>