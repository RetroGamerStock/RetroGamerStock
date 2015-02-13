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
// Utiliser des constantes est particuli�rement interressant avec PHP.
// En plus de fournir un moyen mn�motechnique pour stocker des donn�es,
// les constantes sont "super-globales" : elles peuvent �tre utilis�es
// dans les fonctions sans d�claration.
//_____________________________________________________________________________

define ('BD_ADRESSE', 'localhost');		// Adresse de la base de donn�es
define ('BD_NOM', 'starblags');			// Nom de la base de donn�es
define ('BD_USER', 'starblags_user');	// Nom utilisateur base de donn�es
define ('BD_PASSE', 'starblags_pass');	// Mot de pase base de donn�es

define ('MAIL_SENDER', 'francois.piat@univ-fcomte.fr');
define ('MAIL_SMTP', 'smtp.univ-fcomte.fr');
	
define ('ADRESSE_SITE', 'http://localhost/starblags/');
define ('ADRESSE_PAGE', ADRESSE_SITE.'php/');
define ('ADRESSE_RSS', ADRESSE_SITE.'rss/');

define('FP_DEBUG', TRUE);	// Indicateur pour affichage des messages d'erreur
							// Si TRUE, les messages sont affich�s avec des
							// informations compl�tes.
							// Quand l'appli est test�e et mise en exploitation
							// cette constante devrait �tre d�finie avec FALSE.

define('REP_UPLOAD', '../upload/');	// Nom du r�pertoire d'upload pour les images

define('RC4_CLE', 'fran�ois');	// Cl� pour le cryptage RC4;
define('RC4_SIGNE', 'piat');	// Signature des donn�es crypt�es

// Liens pour bandeau des pages
define('LIEN_AUCUN', 0);	// Pas de lien
define('LIEN_NA_LA', 1);	// Nouvel Article et Liste Article
define('LIEN_MB_NA', 2);	// Mon Blog et Nouvel Article
define('LIEN_MB_LA', 3);	// Mon Blog et Liste Article
define('LIEN_FORM', 4);		// Formulaire d'authentification
define('LIEN_MB_LA_NA', 5);	// Mon Blog et Liste Article et Nouvel Article

//set_magic_quotes_runtime(0);	// On d�sactive la protection automatique des caract�res
	ini_set ("magic_quotes_runtime", 0);
// On d�finit la m�thode de protection des zones pass�es � la base
// dans les r�qu�tes. On le fait ici une fois pour toutes, plut�t
// qu'� chaque appel de la fonction de protection.
if (function_exists('mysql_real_escape_string')) {
	define('FP_PROTECT', 'mysql_real_escape_string');
} elseif (function_exists('mysql_escape_string')) {
	define('FP_PROTECT', 'mysql_escape_string');
} else if (get_magic_quotes_gpc() == 0) {
	define('FP_PROTECT', 'addslashes');
}

// On d�finit PAGE_INDEX si il n'est pas d�j� d�fini.
if (!defined('PAGE_INDEX')) {
	define('PAGE_INDEX', false);
}
//_____________________________________________________________________________
//
//				BASE DE DONNEES
//_____________________________________________________________________________
/**
 * Connexion et selection de la base de donn�es
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
		or exit('Pas de base de donn�es');
}
//_____________________________________________________________________________
/**
 * Affichage d'un message en cas d'erreur sur la base de donn�es.
 * La sortie buff�ris�e est effac�e et un message est affich�,
 * avec plus ou moins d'informations suivant la constante FP_DEBUG.
 * Le traitement est totalement arr�t� (exit()).
 * 
 * @param	string	$Sql	Texte de la requ�te SQL provoquant l'erreur
 * @param	string	$Page	Nom de la page PHP provoquant l'erreur
 * @param	integer	$Ligne	Num�ro de la ligne provoquant l'erreur
 * @global	const	FP_DEBUG	Indicateur de type du message
 */
function fp_bdErr($Sql, $Page, $Ligne) {
	ob_end_clean();  // Effacement de la sortie d�j� buff�ris�e
	
	echo '<html><head><title>Erreur application</title></head><body>';
	if (FP_DEBUG) {
		// Informations longues, � utiliser lors du d�veloppement du site
		// pour avoir un maximum d'info sur les erreurs.
		echo'<table align="center" border=1 cellspacing="0" width="80%" cellpadding="4">',
			'<tr><td colspan="2" bgcolor="#FF0000"><p align="center"><b>',
			'Il semble que nous ayons un probl�me ! </b></td></tr>',
			'<tr><td><p>Fichier</td><td><p>',
			$Page,
			'</td></tr><tr><td><p>Ligne</td><td><p>',
			$Ligne,
			'</td></tr><tr><td><p>Erreur</td><td><p>',
			mysql_errno(),
			'</td></tr><tr><td><p>mySQL</td><td><p>',
			mysql_error(),
			'</td></tr><tr><td><p>Requ�te</td><td><p>',
			$Sql,
			'</td></tr></table>';
	} else {
		// Informations courtes, � utiliser quand le site est sur le web.
		// On pourrait encore r�duire l'affichage des informations et doubler
		// avec un fichier d'erreurs sur le serveur et/ou l'envoi d'un mail
		// au webmaster ou au d�veloppeur.
		echo '<table align="center" border=1 cellspacing="0" width="80%" cellpadding="4">',
			'<tr><td colspan="2" bgcolor="#FF0000"><p align="center"><b>',
			'Il semble que nous ayons un probl�me ! </b></td></tr>',
			'<tr><td><p>Fichier</td><td><p>',
			basename($Page),
			'</td></tr><tr><td><p>Ligne</td><td><p>',
			$Ligne,
			'</td></tr><tr><td><p>Erreur</td><td><p>',
			mysql_errno(),
			'</td></tr></table>';
	}
	echo '</body></html>';
	exit();  // Arr�t total du traitement
}

//_____________________________________________________________________________
//
//				GESTION DE CODE HTML
//_____________________________________________________________________________
/**
 * Les 3 fonctions suivantes (fp_modeleTraite, fp_modeleGet et fp_modeleAffiche)
 * g�rent les traitements des mod�les. On �clate la gestion en 3 fonctions pour
 * plus de modularit� du code, et pour une �volution plus facile si le principe
 * des mod�les �tait revu.
 */
//_____________________________________________________________________________
/**
 * Lecture d'un fichier modele, remplacement des motifs et affichage
 * 
 * @param string	$Fichier	Nom du fichier � lire (sans r�pertoire ni extension)
 * @param array		$Remplace	Tableau associatif des remplacements
 * @return string	Contenu du fichier
 */
function fp_modeleTraite($Fichier, $Remplace) {
	$Modele = fp_modeleGet($Fichier);	// R�cup�ration du modele
	fp_modeleAffiche($Modele, $Remplace);		
}
//_____________________________________________________________________________
/**
 * Lecture d'un fichier modele
 * 
 * @param string	$Fichier	Nom du fichier � lire (sans r�pertoire ni extension)
 * @return string	Contenu du fichier
 */
function fp_modeleGet($Fichier) {
	// Suivant la page en cours, le chemin d'acc�s au fichier mod�le est diff�rent:
	// - la page index.php doit r�f�rencer modeles/$Fichier
	// - les autres pages doivent r�f�rencer ../modeles/$Fichier
	// Pour faire cette diff�rence on utilise $_SERVER['PHP_SELF'] qui contient
	// le chemin d'acc�s et le nom du fichier en cours d'�x�cution (par exemple
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
		ob_end_clean();  // Effacement de la sortie d�j� buff�ris�e
		echo '<html><head><title>Erreur application</title></head><body>',
			'Le fichier ', $Fichier, ' ne peut pas �tre ouvert.',
			'</body></html>';
		exit();  // Arr�t total du traitement
	}
	$Buffer = fread($Pointeur, filesize($Fichier));	// Lecture
	fclose($Pointeur);	// fermeture du fichier
	return $Buffer;
}
//_____________________________________________________________________________
/**
 * Remplacement des motifs d'un modele et affichage
 * 
 * @param string	$Modele		Texte du mod�le
 * @param array		$Remplace	Tableau associatif des remplacements
 */
function fp_modeleAffiche($Modele, $Remplace) {
	// Comme la fonction str_replace accepte des tableaux, pour faire 
	// les remplacements dans les mod�les on utilise un tableau associatif : 
	// - la cl� est l'expression � remplacer 
	// - la valeur est la valeur de remplacement
	// De cette fa�on la fonction ne sera appel�e qu'une fois par modele.
	// On utilisera la fonction array_keys($Remplace) pour r�cup�rer un tableau
	// des cl�s et array_values($Remplace) pour r�cup�rer un tableau des valeurs.

	echo str_replace(array_keys($Remplace), array_values($Remplace), $Modele);
	
	// Il peut sembler "bizarre" d'avoir une fonction avec cette seule ligne.
	// L'explication tient dans la facilit� qu'on aurra plus tard si on a
	// � changer le traitement. Les appels � la fonction devraient rester les
	// m�mes, seule le code de la fonction serait � changer.
}

//_____________________________________________________________________________
/**
 * Renvoie le code html des liens de mise � jour pour le bandeau des pages priv�es
 * 
 * @param	integer	$Type		Type des liens � g�n�rer. Voir constantes LIEN_xxx
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
 * Cette fonction accepte un nombre variable de param�tres.
 * Seul le premier est d�fini dans la d�finition de la fonction.
 * Les param�tres suivants d�finissent les boutons.
 * La d�finition d'un bouton se fait dans une zone alpha de la forme :
 *  Type|Nom|Valeur|JavaScript
 * 	Type	type du bouton
 * 		S : submit
 * 		R : reset
 * 		B : button
 * 	Nom		nom du bouton  (attribut name)
 * 	Valeur	valeur du bouton (attribut value)
 * 	JavaScript	fonction JavaScript pour �v�n�ment onclick
 * 
 * Exemple : fp_htmlBoutons(2, 'B|btnRetour|Liste des sujets|history.back()', 'S|btnValider|Valider'
 * 
 * @param	integer	$Colspan	Nombre de colonnes de tableau � joindre. Si -1 pas dans un tableau
 * @param	string	Ind�fini	D�finition d'un bouton. Il peut y avoir
 * 								autant de d�finitions que d�sir�.
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
 * Affiche le code html d'une ligne de tableau �cran de saisie.
 * 
 * Le code html g�n�r� est de la forme
 * <tr><td> libelle </td><td> zone de saisie </td></tr>
 * 
 * Seuls les 3 premiers param�tres sont obligatoires. Les autres d�pendent
 * du type de la zone.
 * Le libell� de la zone est prot�g� pour un affichage HTML
 * Si la valeur de la zone est du texte, il est prot�g� pour un affichage HTML
 * 
 * @param	string	$Type	type de la zone
 * 							A : textarea
 * 							AN : textarea uniquement en affichage
 * 							C : case � cocher
 * 							H : hidden
 * 							P : password
 * 							R : bouton radio
 * 							S : select (liste)
 * 							T : text
 * 							TN : text  uniquement en affichage
 * @param	string	$Nom	nom de la zone (attribut name)
 * @param	mixed	$Valeur	valeur de la zone (attribut value)
 * 							Pour le type S, c'est l'�l�ment s�lectionn�
 * @param	string	$Lib	libell� de la zone
 * @param	integer	$Size	si type T ou P : longueur (attribut size)
 * 							si type A : longeur (attribut cols)
 * 							si type S : nombre de lignes affich�es (attribut size)
 * 							si type R : 1 = boutons c�te � c�te / 2 = boutons superpos�s 
 * 							si type C : 1 = cases c�te � c�te / 2 = cases superpos�s 
 * @param	mixed	$Max	si type T ou P : longueur maximum (attribut maxlength)
 * 							si type A : nombre de ligne (attribut rows)
 * 							si type R : tableau des boutons radios (valeur => libell�)
 * 							si type C : tableau des case � cocher (valeur => libell�)
 * 							si type S : tableau des lignes de la liste (valeur => libell�)
 * @param	string	$Plus	Suppl�ment (ex : fonction JavaScript gestionnaire d'�v�nement)
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
	//--------------- Zone de type case � cocher
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
		echo 'L\'erreur suivante a �t� d�tect�e ';
	} else {
		echo 'Les erreurs suivantes ont �t� d�tect�es ';
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
 * Protection d'une cha�ne de caract�res pour une utilisation SQL
 * 
 * @param	string	$Texte		Texte � prot�ger
 * @global	string	FP_PROTECT	Nom de la fonction de protection � utiliser 
 * 
 * @return	string	Cha�ne prot�g�e
 */
function fp_protectSQL ($Texte) {
	if (get_magic_quotes_gpc() == 1) {
		$Texte = stripslashes($Texte);
	}
	// On est oblig� de passer par une variable car on ne peut pas faire de 
	// "constantes fonctions" comme on peut faire des "variables fonctions".
	$Fonction = FP_PROTECT;
	return $Fonction($Texte);
}
//_____________________________________________________________________________
/**
 * Protection d'une cha�ne de caract�res pour un affichage HTML
 * 
 * @param	string	$Texte	Texte � prot�ger
 * @param	boolean	$BR		TRUE si remplacement des saut de ligne par le tag <br>
 * 
 * @return	string	Code HTML g�n�r�
 */
function fp_protectHTML($Texte, $BR = FALSE) {
	return ($BR) ? nl2br(htmlspecialchars($Texte)) : htmlspecialchars($Texte);
}
//_____________________________________________________________________________
/**
 * Enl�ve la protection automatique de caract�res (magic_quotes_gpc) sur $_POST
 * 
 * Si la variable de configuration PHP magic_quotes_gpc a la valeur 1
 * certains caract�res des zones de formulaires sont automatiquement prot�g�s.
 * Cel� peut poser probl�me quand on veut par exemple r�afficher les infos
 * saisies. Cette fonction permet d'enlever les caract�res de protection
 * trouv�s dans les l�l�ments du tableau $_POST.
 * 
 * @global	array	$_POST	Les �l�ments du formulaire
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
 * Cryptage / decryptage d'une cha�ne avec l'algorithme RC4
 *
 * @param	string	$Texte	Donn�es � crypter
 * @param	string	$Cle	Cl� de cryptage
 * 
 * @return string	Donn�es crypt�es ou d�crypt�es
 */

 
 function fp_RC4($Texte, $Cle) {
    $Cles = array();	// tableau initialis� avec les octets de la cl�
    $Etats = array();	// table d'�tats : flux appliqu� sur le texte clair
    $Tmp = '';
    $CleLong = strlen($Cle);
	$TexteLong = strlen($Texte);
	$RC4 = '';
	
	// Premi�re �tape : cr�ation de 2 tableaux de 256 octets en fonction de la cl�
	// Le tableau $Cles est initialis� avec les octets de la cl�
	// Le tableau $Etats est initialis� avec les nombres de 0 � 255 permut�s 
	// pseudo-al�atoirement selon le tableau K.
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
    
    // Deuxi�me �tape : permutations pour le chiffrement/d�chiffrement. 
    // Toutes les additions sont ex�cut�es modulo 256.
	// Le tableau $Etats change � chaque it�ration en ayant deux �l�ments permut�s.
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
* Composition d'une URL avec cryptage des param�tres
*
* Les param�tres de l'URL sont mis les uns � la suite des autres, s�par�s par
* le caract�re | (pipe). On ajoute en d�but de la cha�ne des param�tres la
* signature de cryptage. La cha�ne est ensuite prot�g�e pour les caract�res 
* sp�ciaux d'URL. Elle est ajout�e � l'URL avec comme nom x. On obtient ainsi
* par exemple : mapage.php?x=HKVSkS6t
* 
* @param	string 	$Url		D�but de l'url (ex : mapage.php)
* @param	mixed 	$X		Param�tres de l'url. Ils sont d'un nombre ind�termin�
* @global	string	RC4_SIGNE	Signature de cryptage
* @global	string	RC4_CLE		Cle de cryptage
* 
* @return string	URL crypt�e
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
 * D�cryptage d'un param�tre GET et renvoi des valeurs contenues
 * 
 * Cette fonction est en quelque sorte l'inverse de de fp_makeURL.
 * Elle r�cup�re la vairable $_GET['x'], la d�crypte, v�rifie la signature
 * puis renvoie les diff�rentes valeurs trouv�es sous la forme d'un tableau.
 * Le script est arr�t� si
 * - le param�tre x est absent
 * - la signature n'est pas bonne
 * - il n'y a pas plus de une valeur
 * 
 * @global	array	$_GET['x']	Param�tre de la page
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
* @return string cha�ne jj/mm/aaaa
*/
function fp_amjJma($Date) {
	if ($Date == 0) {
		return '';
	}
	return preg_replace("/(\d{4})(\d{2})(\d{2})/","\\3/\\2/\\1",$Date);
}
//_____________________________________________________________________________
/**
* V�rification de la session d'un utilisateur.
* 
* A utiliser dans les pages de mise � jour pour s'assurer qu'une session
* est bien initialis�e pour l'utiliseur.
* Si ce n'est pas le cas, l'utilisateur est redirig� sur la page d'acceuil.
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
	
	// Composition de l'ent�te du mail
	$EnTete = 'From: "StarBlags" <'.MAIL_SENDER.'>'.$FinLigne
			.'Reply-To: '.MAIL_SENDER.$FinLigne
			.'MIME-Version: 1.0'.$FinLigne
			.'Content-Type: text/html; charset=iso-8859-1'.$FinLigne
			.'X-Priority: 1'.$FinLigne
			.'X-Mailer: PHP / '.phpversion().$FinLigne;
			
	// Envoi du mail
	if (!mail($Destinataire, $Objet, $Texte, $EnTete)) {
		$Msg = __LINE__.' - '.basename(__FILE__)
			.' - Un mail n\'a pas pu �tre envoy�.<br>'
			.'<a href="mailto:'.MAIL_SENDER.'">'
			.'Pr�venez l\'administrateur du site.</a>';
		exit($Msg);
	}			
}
//_____________________________________________________________________________
/**
 * R�cup�ration de l'adresse IP du visiteur
 * 
 * @return	string	Adresse Ip du visiteur ou '' si impossible � d�terminer
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
 * Si il n'y a pas d'images li�es, le texte est simplement
 * affich� � la suite de l'ent�te.
 * Si il y a des images li�es :
 * - on utilise un bloc pour les images du haut
 * - on utilise un bloc pour les images du bas
 * - pour les images de gauche, de droite et pour le texte
 * on utilise un tableau. C'est le plus simple pour �viter
 * des "bidouilles" pour l'alignement vertical des images.
 *
 *	 ____________________________________________________
 *  | bloc ent�te                                        |
 *  |____________________________________________________|
 *  | bloc image haut (si n�cessaire)                    |
 *  |____________________________________________________|
 *   ____________________________________________________
 *  | cellule |  texte                         | cellule |
 *  | images  |                                | images  |
 *  | gauche  |                                | droite  |
 *  | ________|________________________________|_________|
 *   ____________________________________________________
 *  | bloc image bas (si n�cessaire)                     |
 *  |____________________________________________________|	
 *   ____________________________________________________
 *  | bloc liens commentaire, note, etc.                 |
 *  |____________________________________________________|	
 * 
 *
 * @param	array	$Articles	Enregistrement table articles
 * @global	const	REP_UPLOAD	R�pertoire de t�l�chargement des images
 */
function fp_articleAffContenu($Articles, $Modele) {
	// Recherche de la note de l'article
	$Sql = "SELECT sum(anNote)
			FROM articles_notes
			WHERE anIDArticle = {$Articles['arID']}";
			
	$R = mysql_query($Sql) or fp_bdErr($Sql, __FILE__, __LINE__);  // Ex�cution requ�te
	
	$BD = mysql_fetch_array($R);	// R�cup�ration de la s�lection
	$Note = $BD[0];
	mysql_free_result($R);
		
	//---------------------------------------------------------------
	// Traitement des images li�es
	// Les tags des cellules images et  l�gendes sont stock�s 
	// dans des matrices PHP qui serviront � construire les tableaux HTML.
	// Matrice : $Images[place][incr�ment]
	// (Rapel place : 0 = haut, 1 = droite, 2 = bas, 3 = gauche)
	$Images = $Illus = array();	
	
	$Remplace = array();	
	$Remplace['@_PHOTO_0_@'] = $Remplace['@_PHOTO_1_@'] = '';
	$Remplace['@_PHOTO_2_@'] = $Remplace['@_PHOTO_3_@'] = '';
		
	// Recherche des images li�es � l'article
	$Sql = "SELECT *
			FROM photos
			WHERE phIDArticle = {$Articles['arID']}
			ORDER BY phNumero";
			
	$R = mysql_query($Sql) or fp_bdErr($Sql, __FILE__, __LINE__);  // Ex�cution requ�te

	while ($BD = mysql_fetch_assoc($R)) {  // Boucle de lecture de la s�lection
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
 	// En-t�te avec le titre de l'article et sa date de parution
 	// Si l'utilisateur qui affiche la page est le cr�ateur du blog
	// il peut modifier un article en cliquant sur son titre.
	$Titre = fp_protectHTML($Articles['arTitre']);		
	if (isset($_SESSION['IDBlog']) && $_SESSION['IDBlog'] == $IDBlog) {
		// Les param�tres du lien sont crypt�s (IDarticle)
		$Url = fp_makeURL('article_maj.php', $Articles['arID']);
		$Titre = '<a href="'.$Url.'">'.$Titre.'</a>';	 	
	}
	$Remplace['@_TITRE_@'] = $Titre;
	$Remplace['@_TEXTE_@'] = $Articles['arTexte'];
						
	//---------------------------------------------------------------
	// Affiche des liens - fin d'un article.
	// - le nombre de commentaires et lien pour en ajouter, 
	// - la note �ventuelle, 
	// - le lien pour noter.
	$Liens = '';
	
	// Si il y a des commentaires pour l'article, on affiche un lien
	// pour l'affichage d'une fen�tre popup avec les commentaires
	if ($Articles['NbComments'] > 0) {
		// Les param�tres du lien sont crypt�s (IDArticle)
		$Url = fp_makeURL('comments_voir.php',$Articles['arID']);
		$Url = "javascript:FP.ouvrePopUp('$Url')";
		
		$Liens .= '<a href="'.$Url.'" class="articleLienCom">'
				.$Articles['NbComments']
				.( ($Articles['NbComments'] == 1) ? ' commentaire</a>':' commentaires</a>');
	}	
	
	// Lien pour la saisie d'un commentaire
	if ($Articles['arComment'] == 1) {
		// Les param�tres du lien sont crypt�s (IDArticle)
		$Url = fp_makeURL('comment_ajout.php', $Articles['arID']);
		$Url = "javascript:FP.ouvrePopUp('$Url')";
		
		$Liens .= '<a href="'.$Url.'" class="articleLienComAjout">ajouter un commentaire</a>';
	}	

    // Note de l'article
	if ($Note > 0) {
		$Liens .= '<a class="articleNote">'.$Note.'</a>'; 
	}

	// Lien pour noter l'article et fin du tableau
	// Les param�tres du lien sont crypt�s (IDArticle)
	$Url = fp_makeURL('article_noter.php', $Articles['arID']);
	$Url = "javascript:FP.ouvrePopUp('$Url')";
	
	$Liens .= '<a href="'.$Url.'" class="articleLienNoteAjout">noter</a>';

	$Remplace['@_LIENS_@'] = $Liens;
	
	fp_modeleAffiche($Modele, $Remplace);	// Remplacement et affichage du modele		
}
?>