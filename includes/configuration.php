<?php
/**
 *  Configuration du site
 *
 * PAGE APPARTENANT AU NOYAU, NE PAS DISTRIBUER SANS AUTORISATION DU CREATEUR
 *
 *  @author Louis "Loode" DEBRAINE <louisdebraine@hotmail.com>
 *  @copyright ©Loode 2009
 *  @link http://traspian.fr.nf
 */
// -- Paramétres de connexion au serveur MySQL.
if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){

	// Serveur local.
		define('SERVEUR_MYSQL', 'localhost');
		define('UTILISATEUR_MYSQL', '');
		define('PASSWORD_MYSQL', '');
		define('BDD_MYSQL', 'traspian_jeu');
		define('URL_SITE', '');

}elseif($_SERVER['SERVER_ADDR'] == '91.121.57.4'){

	// Serveur test.
		define('SERVEUR_MYSQL', 'localhost');
		define('UTILISATEUR_MYSQL', '');
		define('PASSWORD_MYSQL', '');
		define('BDD_MYSQL', 'loode_test');
		define('URL_SITE', 'http://loode.network-hosting.com/');

}else{

	// Serveur distant.
		define('SERVEUR_MYSQL', 'localhost');
		define('UTILISATEUR_MYSQL', 'kdocode');
		define('PASSWORD_MYSQL', '');
		define('BDD_MYSQL', 'kdocode');
		define('URL_SITE', 'http://www.kdocode.com');
}

// -- Constantes générales
define('ROOT', dirname('index.php') . '/');
define('PHP_EXT', substr(__FILE__, strrpos(__FILE__, '.')+1));
define('COMMON', true);

// -- Constantes d'organisation des fichiers.
define('DOSSIER_LIBRAIRIES', 'includes/librairies');
define('DOSSIER_MODULES', 'pages/frames');
define('DOSSIER_LOGS', 'logs');
define('DOSSIER_CACHE', 'caches/compiles/');
define('DOSSIER_TEMPLATES', 'templates/files/');

// -- Constantes de configuration.
define('TEMPLATE_PAR_DEFAUT', '1');
define('MODULE_PAR_DEFAUT', 'index');
define('MDP_ADMINISTRATION', '4AA3C445F0FA6');

// -- Constantes des urls des Codes.
define('FICHIER_TIMESTAMP', 'caches/divers/code_timestamp.php');
define('DOSIER_CODES', 'caches/divers/codes/');

// -- Constantes de temps.
define('ONE_MINUTE', 60);
define('ONE_HOUR', 60 * ONE_MINUTE);
define('ONE_DAY', 24 * ONE_HOUR);
define('ONE_WEEK', 7 * ONE_DAY);
define('ONE_MONTH', 4 * ONE_WEEK);
define('ONE_YEAR', 12 * ONE_MONTH);

// -- Constantes de Payement.
	// -- PAYPAL
define('SERVEUR_PAYPAL', 'https://www.paypal.com/webscr&cmd=_express-checkout&token=');
define('API_PAYPAL', 'https://api-3t.paypal.com/nvp?');
define('VERSION_API_PAYPAL', 57.0);

define('USER_PAYPAL', '');
define('PASS_PAYPAL', '');
define('SIGNATURE_PAYPAL', '');

define('URL_ANNULATION', 'http://kdocode.com/pages/html/annule.html');
define('URL_RETOUR', 'http://kdocode.com/annonceur-payement.html');
define('LOGO_SITE', 'http://kdocode.com/images/logo.png'); 

	// -- ALLOPASS
define('IDENTIFIANT_ALLOPASS', '189013/515441/2957617');


?>