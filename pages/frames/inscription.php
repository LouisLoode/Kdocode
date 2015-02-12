<?php
/**
 * Page d'inscription au tirage au sort.
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	
	// -- Vérification de la variable GET, si elle est vide, on balance un message d'erreur.
		if (empty($_GET['id'])){
		$this->setMsgPhp('Vous avez tenté d\'accéder sur cette page en n\'entrant aucun ID.', 'index.html', 5, 1);
        }
		
		if (empty($_POST['code'])){
		// -- On défini le nom du titre.
        $this->setTitle('Inscription au Tirage au sort');
		
		// -- On défini la description de la page.
        $this->setDesc('Sur cette page, vous pouvez vous inscrire au tirage au sort.');
	
		// -- On défini le template
        $this->setTpl('frames/tirage/inscription.html');
		
		// -- On compte le nbr de participants.
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_participants FROM site_participations'));
		Instances::$tpl->set('NBR_PARTICIPANTS', $res['nbr_participants']);
		Instances::$tpl->set('ID', $_GET['id']);
		
        return;
        }
		
		// -- Sinon, on traite les données
        $this->_registerGame();
    }
	
    /**
     * Vérifie et accepte l'inscription
     * 
     * @return bool
     */
    private function _registerGame(){
	
        // -- On récupére le formulaire.
        $this->data = array('id' => $_GET['id'],
							'email' => $_POST['email'],
							'code' => supr_espaces($_POST['code']));
        
      // -- On vérifie tous les champs
	    // -- Vérification du format de l'adresse mail.
        if (!(preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $this->data['email']))){
			$this->setMsgPhp('L\'adresse email entrée n\'a pas le bon format.', 'index.html', 5, 1);
        }
		
        // -- On vérifie que le code est valide.
        $sql = 'SELECT COUNT(*)
                    FROM site_inscrits
                    WHERE regis_id = "'.Instances::$security->entree($this->data['id']).'" AND
						  regis_mail = "'.Instances::$security->entree($this->data['email']).'" AND
						  regis_validation = "";';
        $res = mysql_fetch_array(Instances::$db->sql_query($sql));
		
		// -- On le dégage si ça existe déjà.
        if ($res[0] != 1){
			// -- On insére une ligne dans le log.
			$this->setMsgPhp('Votre id et votre adresse email n\'existent pas dans notre base de donnée.', 'index.html', 5, 1);
        }
		
		$monfichier = fopen(FICHIER_TIMESTAMP, 'a+');
		$timestamp = fgets($monfichier); // On lit la première ligne
		fclose($monfichier); // On ferme le fichier
		
		// -- Vérifie si un mailing est en cours.
		if (empty($timestamp)){
			$this->setMsgPhp('Il n\'y a aucun mailing en cours.', 'index.html', 5, 1);
		}
		
		$monfichier = fopen(DOSIER_CODES.$timestamp, 'a+');
		$code = fgets($monfichier); // On lit la première ligne
		fclose($monfichier); // On ferme le fichier.
		
		// -- Le fichier n'est pas vide, il y a donc un mailing en cours, pas deux mailing sans tirage.
		if (supr_espaces($code) != $this->data['code']){
			$this->setMsgPhp('Le code de participation est invalide.', 'index.html', 5, 1);
		}
			
		
		
		// -- On vérifie que le code est valide.
        $sql = 'SELECT COUNT(*)
                    FROM site_participations
                    WHERE parti_id_inscrit = "'.Instances::$security->entree($this->data['id']).'";';
        $res = mysql_fetch_array(Instances::$db->sql_query($sql));
		
		// -- On le dégage si ça existe déjà.
        if ($res[0] != 0){
			// -- On insére une ligne dans le log.
			$this->setMsgPhp('Vous vous êtes déjà inscrit à ce tirage au sort.', 'index.html', 5, 1);
        }
		
		// -- Plus de vérification, donc on passe à l'enregistrement dans la table.
        $sql = 'INSERT INTO site_participations (parti_id, parti_id_inscrit)
                VALUES ("","'.Instances::$security->entree($this->data['id']).'");';
        Instances::$db->sql_query($sql);
		
	// -- On finit par afficher le message qui informe le visiteur qu'il est inscrit sur le site.
	$this->setMsgPhp('Votre participation au tirage au sort a bien été prise en compte, vous serez contacté si vous avez gagner le code allopass.', 'index.html', 5, 0);

    }
	
} // end Frame_Child
?>