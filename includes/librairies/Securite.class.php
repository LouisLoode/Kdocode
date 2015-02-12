<?php
/**
 *  Systéme de gestion de la securite des fluxs.
 *
 * PAGE APPARTENANT AU NOYAU, NE PAS DISTRIBUER SANS AUTORISATION DU CREATEUR
 *
 *  @author Louis "Loode" DEBRAINE <louisdebraine@hotmail.com>
 *  @copyright ©Loode 2009

 */
 
class Securite {

	    /**
     * Entrées dans la BDD
     *
     * @param string data --> Données à sécuriser
     * @access public
     * @return $data
     */
	public function entree($data)
		{
			// -- On regarde si le type de string est un nombre entier (int)
			if(ctype_digit($data))
			{
				$data = intval($data);
			}
			// -- Pour tous les autres types
			else
			{
				$data = mysql_real_escape_string($data);
				$data = addcslashes($data, '%_');
			}
				
			return $data;
		}

	    /**
     * Sorties de la BDD
     *
     * @param string data --> Données à sécuriser
     * @access public
     * @return $data
     */
	public function sortie($data)
		{
			return stripslashes($data);
		}

	    /**
     * Hacher le mot de passe
     *
     * @param string data --> Données à sécuriser
     * @access public
     * @return $data
     */
	public function hacher($data)
		{
		  global $config;
			return md5(sha1(PREFIXE_HACHAGE) . $data . sha1(SUFFIXE_HACHAGE));
		}

	    /**
     * Prend l'adresse réelle du visiteur
     *
     * @access public
     * @return ip
     */
	public function getIp($ip2long = true){
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }
    
    if ((bool)$ip2long) {
        $ip = ip2long($ip);
    }
    
    return $ip;
		}

	    /**
     * Ajoute une ligne dans le fichier log.
     *
     * @param string $phrase -> Phrase à mettre dans le log.
     * @param string $phrase -> Fichier ou on met la phrase de log.
     * @access public
     * @return $data
     */
	public function addLog($phrase, $fichier)
		{
			// -- On ouvre le fichier.
			$fichier_ouvert = fopen(DOSSIER_LOGS .'/'. $fichier.'.txt', 'a+');
			
			// -- On défini la ligne
			$texte = date("\L\e d/m/Y à (H:i:s)")." : ".$phrase."\r\n";
			
			// -- On écrit la ligne dans le fichier.
			fwrite($fichier_ouvert, $texte);
			
			// -- On ferme le fichier.
			fclose($fichier_ouvert);
		}

	    /**
     * Si une adresse IP est déjà bannie.
     *
     * @param string $phrase -> Adresse ip.
     * @access public
     * @return true or false
     */
	public function isBan($ip)
		{
			return strpos(ip2long($ip) . '\n', file_get_contents(FILE_BAN)) === false ? false : true;
		}

	    /**
     * Ajoute une adresse dans le fichier des adresses bannies.
     *
     * @param string $phrase -> Adresse ip.
     * @access public
     * @return true or false
     */
	public function addBan($ip)
		{
		if(empty($ip)){
		$ip = $this->getIp();
		}
			if ($this->isBan(long2ip($ip))) {
                    $fichier = fopen(FILE_BAN, 'a+'); // -- On ouvre en mode 'a+'
                    fwrite($fichier, $ip . "\n"); // -- On ajoute la ligne avec l'ip
                    fclose($fichier); // -- On ferme le fichier    
                    echo 'Cette adresse est désormais non-autorisée.';
            }
		}

	    /**
     * Génére une clé aléatoire
     *
     * @access public
     * @return key
     */
	public function genKey()
		{
			return md5(uniqid(rand(), true));
		}
		
		/**
     * Vérifie l'autorisation d'accés de l'utilisateur
     *
     * @param string $phrase -> Adresse ip.
     * @access public
     * @return true or false
     */
	public function checkAcces($lvlRequis)
		{
		// -- D'abord on vérifie si le membre est connecté, si il ne l'est pas on lui attribut le niveau d'accés 1.
			((!empty($_SESSION['membre'])) ? $lvlMembre = intval($_SESSION['membre']['rang']['niveau']) : $lvlMembre = 1);
		// -- Ensuite on vérifie si le niveau d'accés est égal ou supérieur au niveau nécéssaire.
			(($lvlMembre >= $lvlRequis) ? $access = true : $access = false);
	  // -- Renvoit la valeur d'accés qui défini si la personne à accés ou non.
	  return $access;
		}

}

?>