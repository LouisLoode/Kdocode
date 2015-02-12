<?php
/**
 * Page d'accueil
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	
	if (!isset($_POST['email'])){
		// -- On défini le nom du titre.
        $this->setTitle('Page d\'acceuil');
		
		// -- On défini la description de la page.
        $this->setDesc('La page d\'accueil vous permet de vous inscrire sur le jeu.');
	
		// -- On défini le template
        $this->setTpl('frames/portail/index.html');
		
		// -- On va supprimer les publicitées qui sont tombées à 0 affichages.
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_pubs_delete FROM site_publicites WHERE pub_valide = "1" AND pub_nbr_affiche <= 0'));
		if ($res['nbr_pubs_delete'] > 0) {
			
			// -- On effectue la requéte qui va supprimer toutes les publicitées qui sont à 0 ou en dessous.
				$sql = 'DELETE FROM site_publicites WHERE pub_valide = "1" AND pub_nbr_affiche <= 0';
				$res = Instances::$db->sql_query($sql);
		}
		
		// -- On compte le nbr d'inscrit comme ça les membres viennent pas gueuler pour savoir le nombre d'inscrit qu'il y a. :p
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_inscrits FROM site_inscrits'));
		Instances::$tpl->set('NBR_INSCRITS', $res['nbr_inscrits']);
		
            return;
         }
		
		// Maintenant, on va selectionner l'action à effectuer, on l'a renseignée au début
			if ($_POST['action'] == 'unregister')
			{
				// -- Sinon, on traite les données
				$this->_unregisterMember();
				return; 
			}else{
				// -- Sinon, on traite les données
				$this->_registerMember();
				return; 
			}
    }
	
    /**
     * Vérifie et enregistre le membre
     * 
     * @return bool
     */
    private function _registerMember(){
	
	
        // -- On récupére le formulaire.
        $this->data = array();
        $this->data['email'] = $_POST['email'];
        $this->data['info'] = $_POST['info'];
		
      // -- On vérifie tous les champs

          // -- Vérification du captcha et si il y a une merde, on refile le probléme aux autres ^^
        if (!isset($this->data['info'])){
			$this->setMsgPhp('L\'adresse email n\'a pas le bon format.', 'google.com', 5, 1);
        }
	
        if (!(preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $this->data['email']))){
			$this->setMsgPhp('L\'adresse email n\'a pas le bon format.', 'index.html', 5, 1);
        }	
		
	// -- Interdit les adresses Yopmails
        if (preg_match('#^[a-z0-9._-]+@yopmail.[a-z]{2,4}$#', $this->data['email'])){
			$this->setMsgPhp('Les adresses Yopmail sont interdites.', 'index.html', 5, 1);
        }
		
        // -- On vérifie si le Login ou le Mail existent déjà dans la table.
        $sql = 'SELECT COUNT(*)
                    FROM site_inscrits
                    WHERE regis_mail = "'.Instances::$security->entree($this->data['email']).'";';
        $res = mysql_fetch_array(Instances::$db->sql_query($sql));
		
        // -- On le dégage si ça existe déjà.
        if ($res[0] != 0){
		
			// -- On insére une ligne dans le log.
			Instances::$security->addLog('Un visiteur a tenté de s\'inscrire mais son email ('.$this->data['email'].') existe déjà dans la table.', 'piratages');
			$this->setMsgPhp('Il existe déjà un membre avec cette adresse email.', 'index.html', 5, 1);
        }
        
		// -- On génére la clé de validation..
		$cleValidation = strtoupper(uniqid());
		
		// -- Plus de vérification, donc on passe à l'enregistrement dans la table.
        $sql = 'INSERT INTO site_inscrits (regis_id, regis_mail, regis_validation, regis_site, regis_date_inscrit)
                    VALUES ("","'.Instances::$security->entree($this->data['email']).'", "'.$cleValidation.'", "1", "'.time().'");';
        Instances::$db->sql_query($sql);
		
		// -- On récupére l'id du nouveau membre.
		$idNewMember = mysql_insert_id();
		
	// -- On configue le mail.
		// -- Le template du mail
        Instances::$tpl->set(array(
                'ID' => $idNewMember,
                'CODE' => $cleValidation));

	//Headers du mail
		$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
		$headers .= 'To:  <'.$this->data['email'].'>'."\n"; // pour XXX
		$headers .= 'Reply-To: <mail@kdocode.com>'."\n"; // repondre à
		$headers .= 'Return-Path: <mail@kdocode.com>'."\n"; // Pour la réponse (la fonction répondre à l'expéditeur)
		//et Le renvoi du message s'il ne peut arriver au destinataire
		//$headers .= 'Cc: '."\n"; // destinataire en copy carbone
		//$headers .= 'Bcc: '."\n"; // destinataire en blind copy carbone
		$headers .= 'X-Priority: 1'."\n"; // niveau de priorité meme si c'est illusoire
		//$headers .= 'Disposition-Notification-To: mail@kdocode.com'."\n"; // accuser de reception
		$headers .= 'MIME-Version: 1.0'."\n"; // version du mime
		$headers .= 'Content-Type: text/html; charset="utf-8"'."\n";
		// type de mail html
		$headers .= 'Content-Transfer-Encoding: 8bit'; // niveau d'encodage 7 oui 8 bit
        
        // -- On envoi le mail...
        mail($this->data['email'], 'Votre inscription sur KDOCode.com', Instances::$tpl->pparse('mails/validation.html'), $headers); 

	// -- On finit par afficher le message qui informe le visiteur qu'il est inscrit sur le site.
	$this->setMsgPhp('Vous avez bien été inscrit, vous devez cependant valider votre compte grâce à la clé qui vous a été envoyée par email. <b><u>Il se peut que le mail se trouve dans le dossier indésirable de votre messagerie.</u></b>', 'index.html', 10, 0);
	
    } 
	
	/**
     * Vérifie et désinscris le membre
     * 
     * @return bool
     */
    private function _unregisterMember(){
	

        // -- On récupére le formulaire.
        $this->data = array();
        $this->data['email'] = $_POST['email'];
		$this->data['info'] = $_POST['info'];
		
      // -- On vérifie tous les champs

	    // -- Vérification du captcha et si il y a une merde, on refile le probléme aux autres ^^
        if (!isset($this->data['info'])){
			$this->setMsgPhp('L\'adresse email n\'a pas le bon format.', 'google.com', 5, 1);
        }
		
        // -- Vérification du format de l'adresse mail.
        if (!(preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $this->data['email']))){
			$this->setMsgPhp('L\'adresse email n\'a pas le bon format.', 'index.html', 5, 1);
        }
		
        // -- On vérifie si le Mail existe déjà dans la table.
        $sql = 'SELECT COUNT(*)
                    FROM site_inscrits
                    WHERE regis_mail = "'.Instances::$security->entree($this->data['email']).'";';
        $res = mysql_fetch_array(Instances::$db->sql_query($sql));
		
        // -- On le dégage si ça n'existe pas.
        if ($res[0] != 1){
			$this->setMsgPhp('Il n\'existe aucun un membre avec cette adresse email.', 'index.html', 5, 1);
        }
		
		// -- Plus de vérification, donc on passe à la suppression dans la table.
        $sql = 'DELETE FROM site_inscrits WHERE regis_mail = "'.Instances::$security->entree($this->data['email']).'";';
        Instances::$db->sql_query($sql);

	// -- On finit par afficher le message qui informe le visiteur qu'il est inscrit sur le site.
	$this->setMsgPhp('Vous avez bien été désinscrit de KdoCode.com', 'index.html', 10, 0);

    } 
	
} // end Frame_Child
?>