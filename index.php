<?php
/**
 *  Index du site
 *
 * Merci à Talus (http://talus-works.net) pour les fabuleux scripts qu'il fournit.
 *
 * @moteur de template: TalusTPL 1.5.0
 * @moteur sql: TraSQL 1.0.1
 *
 * PAGE APPARTENANT AU NOYAU, NE PAS DISTRIBUER SANS AUTORISATION DU CREATEUR
 *
 *  @author Louis "Loode" DEBRAINE <louisdebraine@hotmail.com>
 *  @copyright ©Loode 2009
 *  @link http://traspian.fr.nf
 */


// -- On démarre les sessions.
session_start();

// -- On défini le fuseau horaire.
date_default_timezone_set('Europe/Paris');

// -- On gére les erreurs.
error_reporting(E_ALL | E_STRICT);

// -- Envoi de l'header UTF-8
header("Content-Type : text/html;charset=utf-8");

// -- Inclusion du fichier de configuration.
include 'includes/configuration.php';

// -- Inclusion des fonctions.
include 'includes/fonctions.php';

// -- On regarde si on est en test ou en production.
if($_SERVER['SERVER_ADDR'] == '91.121.57.4'){
echo '<center><b><u><h1>Serveur de Béta test, forum accessible <a href="forum/">ici</a></h1></u></b></center>';
}


// -- Vérifier si cette adresse ip est celle du W3C si elle l'est, en la renvoie vers une page valide Gniarrk !!
if (preg_match("#128.30.52#", $_SERVER["REMOTE_ADDR"]))
{
        exit(readfile('pages/html/w3c.html'));
}

/**
 * Contient les principaux objets du site
 * 
 * @access  public
 */
final class Instances {
    public static $tpl = null;
    public static $db = null;
    public static $security = null;
    public static $frame = null;
}

/**
 * Démarrage des classes
 *
 * @abstract
 */

// -- On inclut et démarre Talus' TPL
include DOSSIER_LIBRAIRIES.'/talus_tpl_filters.php';
include DOSSIER_LIBRAIRIES.'/talus_tpl_compiler.php';
include DOSSIER_LIBRAIRIES.'/talus_tpl_cache.php';
include DOSSIER_LIBRAIRIES.'/talus_tpl.php';

// -- On démarre le moteur. En premier argument se situe l'endroit des tpls, en deuxième l'emplacement du cache.
Instances::$tpl = new Talus_TPL(DOSSIER_TEMPLATES . TEMPLATE_PAR_DEFAUT .'/', DOSSIER_CACHE);

// -- Si la page maintenance.html existe, on la lit et on arrete l'affichage de l'index.
if (is_file('maintenance.txt'))
{
        Instances::$tpl->set('RAISON', file_get_contents('maintenance.txt'));
		Instances::$tpl->parse('maintenance.html');
		exit();
}

// -- On se connecte à la Base de donnée.
include DOSSIER_LIBRAIRIES.'/MySQL.class.php';
Instances::$db = new MySQL(SERVEUR_MYSQL, UTILISATEUR_MYSQL, PASSWORD_MYSQL, BDD_MYSQL);

// -- On démarre la classe de sécurité.
include DOSSIER_LIBRAIRIES.'/Securite.class.php';
Instances::$security = new Securite();


/**
 * Gère les Pseudos Frames de Traspian.
 *
 * @abstract
 */
abstract class Frame {

	// -- Frame en cours d'utilisation
    protected static $frame = 'portail';
	
	// -- Titre de la page en cours
	private $_title = 'Accueil du site';
	
	// -- Description de la page en cours
	private $_desc = 'Les nouveautées et les derniers évenements du jeu.';
    
	// -- Template à utiliser pour le parsage
    private $_tpl = '';
	
	// -- Messages côté template
	private $_msgTpl = '';
	
	// -- Les fichiers CSS à utiliser
    private $_css = array();
	
	// -- Les fichiers JS à utiliser
    private $_js = array();
	
	// -- L'id du membre actuel
	private $_idMembre = '';
    
    
    /**
     * Démarre la page demandée : à définir dans toutes les frames utilisées.
     * 
     * @return  void
     * @access  protected
     */
    abstract protected function main();
    
    /**
     * Définit les variables du header et du footer.
     * 
     * @return  void
     * @access  private
     */
    final private function _paramsGeneraux(){

		
        $vars = array(
                'TITLE' => $this->_title,
                'SITE_DESC' => $this->_desc,
                'MSG_TPL' => $this->_msgTpl,

				'CSS' => $this->_css,
				'JS' => $this->_js
            );
        
        Instances::$tpl->set($vars);
	
    }
    
    /**
     * Construit la pseudo-frame
     * 
     * @param integer $chrono Chrono démarré dans start.php.
     * @return  void
     * @access  protected
     */
    final protected function __construct(){
        // -- On met une référence à cet objet sur l'objet "Frame".
        Instances::$frame = &$this;
        
        // -- Appel du corps, et du header / footer de la page.
        $this->main();
        $this->_paramsGeneraux();
        
		
        Instances::$tpl->parse($this->_tpl);
    }
    
    /**
     * Récupère la page courante, et démarre la bonne frame.
     * 
     * @param integer $chrono Chrono démarré dans start.php.
     * @param string $page Utiliser une page spécifique ?
     * @return Frame_Child
     * @access public
     * @static
     */
    final public static function start($frame = ''){
        if( empty($frame) ){
            $frame = isset($_GET['frame']) ? $_GET['frame'] : MODULE_PAR_DEFAUT;
        }

    if (!preg_match('`^[a-z0-9_]+$`', $frame) || !file_exists(DOSSIER_MODULES .'/'. $frame . '.php')) {
		self::setMsgPhp('Cette Frame n\'existe pas.', 'index.html', 5, 1);
    }
        
        self::$frame = $frame;
        
        include DOSSIER_MODULES .'/'. self::$frame . '.php';
        
        // -- Lancement de la frame.
        return new Frame_Child();
    } // end Frame::start()
 

     /**
     * On défini les méthodes qui vont gérer diférents paramétres.
     * 
     * @param {les paramétres sont tous différents}.
     * @access public
     * @static
     */
 
	
    final public function setTpl($tpl){
        $this->_tpl = $tpl;
    } // end setTpl()
    
    final public function setTitle($title){
        $this->_title = $title;
    } // end setTitle()
	
	final public function setDesc($desc){
        $this->_desc = $desc;
    } // end Frame::setDesc()
	
     /**
     * On défini les méthodes qui vont gérer diférrentes fonctions.
     * 
     * @param {les paramétres sont tous différents}.
     * @access public
     * @static
     */
	
	final public function setMsgTpl($msg){
        $this->_msgTpl = $msg;
    } // end Frame::addMsgTpl()

	final public static function setMsgPhp($msg, $redirect, $tps=5, $type=0){
		message($msg, $redirect, $tps, $type);
		exit;
    } // end Frame::addMsgPhp()
    

} // end Frame

// -- Appel de la pseudo frame à afficher, génération du header et du footer.
Frame::start();

?>