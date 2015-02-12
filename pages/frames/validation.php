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
	
	// -- Vérification des deux variables GET, si une des deux est vide, on balance un message d'erreur.
	if (multi_empty(trim($_GET['id']), $_GET['code'])){
		$this->setMsgPhp('Vous avez tenté d\'accéder sur cette page en n\'entrant aucun ID et aucun CODE, hors, vous ne pouvez pas.', 'index.html', 5, 1);
        }
		
		// -- Sinon, on traite les données
        $this->_checkMember();
        return;
    }
	
    /**
     * Vérifie et envoie le formulaire
     * 
     * @return bool
     */
    private function _checkMember(){
	
        // -- On récupére le formulaire.
        $this->data = array('id' => $_GET['id'],
							'code' => $_GET['code']);
        
      // -- On vérifie tous les champs
		
        // -- On vérifie que le code est valide.
        $sql = 'SELECT COUNT(*)
                    FROM site_inscrits
                    WHERE regis_id = "'.Instances::$security->entree($this->data['id']).'" &&
						  regis_validation = "'.Instances::$security->entree($this->data['code']).'";';
        $res = mysql_fetch_array(Instances::$db->sql_query($sql));
		
		// -- On le dégage si ça existe déjà.
        if ($res[0] != 1){
			// -- On insére une ligne dans le log.
			$this->setMsgPhp('Votre code n\'est pas valide, ou bien il a été déjà validé.', 'index.html', 5, 1);
        }
		
		// -- Plus de vérification, donc on passe changement de la validation.
        $sql = 'UPDATE site_inscrits SET regis_validation = "" WHERE regis_id = "'.Instances::$security->entree($this->data['id']).'"';
        Instances::$db->sql_query($sql);
		
	// -- On insére une ligne dans le log.
	Instances::$security->addLog('L\'id '.$this->data['id'].' a bien validé son inscription.', 'site');

	// -- On finit par afficher le message qui informe le visiteur qu'il est inscrit sur le site.
	$this->setMsgPhp('Votre inscription a bien été validée, vous pouvez dès maintenant commencer à jouer à KDOCode.', 'index.html', 5, 0);

    }
	
} // end Frame_Child
?>