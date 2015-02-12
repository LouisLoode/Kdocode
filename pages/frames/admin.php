<?php
/**
 * Frame d'administration
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {  
  
    /**
     * Condition pour mailing
     *
     * @var integer
     */
	private $_mailing = true;
	

	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	
// -- On utilise un switch afin de savoir quelle action utiliser (ici, on a le choix entre "accueil", "connexion", "mailing" et "tirage")
switch ($_GET['action']) { 

case "connexion": // -- Dans ce cas, on veux juste se connecter.

		// -- Si le formulaire est pas envoyé, on affiche le formulaie de connexion.
        if (!isset($_POST['pass'])) {
            
				// -- On défini le nom du titre.
				$this->setTitle('Connexion à l\'Administration');
		
				// -- On défini la description de la page.
				$this->setDesc('Cette page vous permet de vous connecter à l\'administration du site.');
		
				// -- On défini le template.
				$this->setTpl('frames/admin/connexion.html');
            return;
        }
        
        // -- Sinon, on traite les données
        $this->_login();
        return;

break; // -- Fin de l'action "connexion".

case "accueil": // -- Accueil de l'administration

	// -- On défini le nom du titre.
	$this->setTitle('Accueil de l\'Administration');
		
	// -- On défini la description de la page.
	$this->setDesc('Cette page vous permet de vous repérer dans l\'administration du site.');
		
	// -- On défini le template.
	$this->setTpl('frames/admin/accueil.html');
	
	// -- On va vérifier qu'il n'y ai pas déjà eu un mailing en cours.
	$monfichier = fopen(FICHIER_TIMESTAMP, 'a+');
	$timestamp = fgets($monfichier); // On lit la première ligne
	fclose($monfichier); // On ferme le fichier.
	
	// -- Le fichier est vide, il y a donc pas de mailing en cours.
	if (empty($timestamp)){
		$this->_mailing = false;
	}else{
		$this->_mailing = true;
	}
	Instances::$tpl->set('MAILING_EN_COURS', $this->_mailing);

 

break; // -- Fin de l'action "accueil".

case "mailing": // -- Envois d'un mailing.
		
	// -- Le type est pas connecté, il peut pas effectuer d'action.
    if (empty($_SESSION['admin'])) {
		// -- Message d'erreur en PHP.
        $this->setMsgPhp('Vous n\'êtes pas connecté, vous ne pouvez donc pas administrer le site.', 'admin-connexion.html', 5, 1);
    }
		// -- Si pas de formulaire envoyé...
        if (empty($_POST['valide'])){
			
			// -- On défini le nom du titre.
			$this->setTitle('Envoyer un mailing');
		
			// -- On défini la description de la page.
			$this->setDesc('Cette page vous sert à envoyer un mailing aux membres.');
	
			// -- On défini le template
			$this->setTpl('frames/admin/mailing.html');
				
            return;
        }
		// -- Sinon, on traite les données
        $this->_sendMailing();
        return;
		
break; // -- Fin de l'action "mailing".

case "tirage": // -- Tirage au sort.
		
	// -- Le type est pas connecté, il peut pas effectuer d'action.
    if (empty($_SESSION['admin'])) {
		// -- Message d'erreur en PHP.
        $this->setMsgPhp('Vous n\'êtes pas connecté, vous ne pouvez donc pas administrer le site.', 'admin-connexion.html', 5, 1);
    }
		// -- Si pas de formulaire envoyé...
        if (!isset($_POST['code_allopass'])){
			
			// -- On défini le nom du titre.
			$this->setTitle('Effectuer un tirage au sort');
		
			// -- On défini la description de la page.
			$this->setDesc('Cette page vous sert à effectuer un tirage au sort sur les participants.');
	
			// -- On défini le template
			$this->setTpl('frames/admin/tirage.html');
			
			// -- On compte le nbr de participants.
			$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_participants FROM site_participations'));
			Instances::$tpl->set('NBR_PARTICIPANTS', $res['nbr_participants']);
				
            return;
        }
		// -- Sinon, on traite les données
        $this->_makeTirage();
        return;
		
break; // -- Fin de l'action "tirage".

default: // -- Par défaut, afin de ne prendre aucun risque on n'autorise pas aucun défaut à par la message d'erreur.
	// -- Le type est pas connecté, il peut pas effectuer d'action.
    if (empty($_SESSION['admin'])) {
		// -- Message d'erreur en PHP.
        $this->setMsgPhp('Redirection vers la page de connexion', 'admin-connexion.html', 0, 3);
    }
		$this->setMsgPhp('Vous n\'avez pas choisi d\'action à effectuer. Vous allez être automatiquement redirigé vers l\'accueil de l\'administration.', 'admin-accueil.html', 5, 1);
}
}

    /**
     * Vérifie le formulaire, et connecte l'administrateur si tout est OK.
     * 
     * @return bool
     * @access private
     */
    private function _login(){
        // -- On renseigne $this->data.
        $this->data = array(
                'pass' => $_POST['pass']
            );
        
		// -- Si on n'essaye pas de nous attaquer par force brute 
		if($this->data['pass'] == MDP_ADMINISTRATION)
		{

					// -- Il n'y a pas eu d'erreurs, donc on crée la session membre qui est un array qui réunit toutes les infos du membre.
					$_SESSION['admin'] = true;
		
			// -- Pis on balance le message PHP qui anonce qu'on est connecté...
			$this->setMsgPhp('Vous êtes maintenant connecté.', 'admin-accueil.html', 5, 0);
	
		// -- Si on a dépassé les 10 tentatives
		}
		// -- Pis on balance le message PHP qui anonce qu'on est connecté...
			$this->setMsgPhp('Le Mot de pass n\'est pas bon', 'index.html', 5, 1);
    }

    /**
     * Envois le mailing.
     * 
     * @return bool
     * @access private
     */
    private function _sendMailing(){
        // -- On renseigne $this->data.
        $this->data = array(
                'textarea' => $_POST['textarea']
            );
	
	// -- On va d'abord vérifier qu'il n'y ai pas déjà eu un mailing en cours.
		$monfichier = fopen(FICHIER_TIMESTAMP, 'a+');
		$timestamp = fgets($monfichier); // On lit la première ligne
		fclose($monfichier); // On ferme le fichier.
			// -- Le fichier n'est pas vide, il y a donc un mailing en cours, pas deux mailing sans tirage.
			if (!empty($timestamp)){
				$this->setMsgPhp('Vous ne pouvez pas lancer plus un nouveau mailing si il y en a déjà un en cours.', 'admin-accueil.html', 5, 1);
			}

	
	// -- On va ensuite créer le nouveau code.
		$new_timestamp = time();
		$monfichier = fopen(DOSIER_CODES.$new_timestamp, 'a+');
		fseek($monfichier, 0); // On remet le curseur au début du fichier
		$newCode = chiffre_alea().' '.chiffre_alea(); // Création d'une nouveau code d'inscription
		fputs($monfichier, $newCode); // On écrit le nouveau code
		fclose($monfichier); // On ferme le fichier.
	
	// -- Pour finir, on inscrit le timestamp qui est le nom dut fichier dans un fichier.
		$monfichier = fopen(FICHIER_TIMESTAMP, 'a+');
		fseek($monfichier, 0); // On remet le curseur au début du fichier
		fputs($monfichier, $new_timestamp); // On écrit le nouveau code
		fclose($monfichier); // On ferme le fichier.
		
			// -- On crée le tableau qui stocke les publicitées avec aucune valeurs par défaut.
		$publicitees = array(
// -- Banniéres de 125X125px.
				// -- Banniére 1
                '1_125X125' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									 'url_image' => 'http://kdocode.com/images/publicites/pub125.gif'),
									 
				// -- Banniére 2
                '2_125X125' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									 'url_image' => 'http://kdocode.com/images/publicites/pub125.gif'),
									 
				// -- Banniére 3
                '3_125X125' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									 'url_image' => 'http://kdocode.com/images/publicites/pub125.gif') ,

// -- Banniéres de 468X60px.
				// -- Banniére 1
                '1_468X60' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									'url_image' => 'http://kdocode.com/images/publicites/pub468.gif'),

// -- Banniéres de 728X90px.
				// -- Banniére 1
                '1_728X90' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									'url_image' => 'http://kdocode.com/images/publicites/pub728.gif'),

				// -- Banniére 2
				'2_728X90' => array('url_site' => 'http://kdocode.com/annonceur-accueil.html',
									'url_image' => 'http://kdocode.com/images/publicites/pub728.gif')
			   );		

// -- On Effectue les requétes quoi vont chercher les publicitées.
		// -- On s'occupe des banniére des 125X125px.
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_pubs_125 FROM site_publicites WHERE pub_valide = "1" AND pub_taille = "125X125" AND pub_nbr_affiche > 0'));
		if ($res['nbr_pubs_125'] > 0) {
			// -- Une petite condition pour savoir combien de pubs on va selectionner
			$selectNbrBan = ($res['nbr_pubs_125'] >= 3) ? $res['nbr_pubs_125'] : 3;
			
			// -- On effectue la requéte qui va chercher les publicités de 125X125px.
				$sql = 'SELECT pub_id, pub_taille, pub_url_site, pub_url_image, pub_nbr_affiche 
															FROM site_publicites 
															WHERE pub_valide = "1" AND pub_taille = "125X125" AND pub_nbr_affiche > 0
															LIMIT 0, '.$selectNbrBan.';';
				$res = Instances::$db->sql_query($sql);
					
					// -- Maintenant on remplit le tableau avec les données.
					$nbrPubs = 1;
					while ($data = mysql_fetch_array($res))
					{
						// -- On remplit le tableau.
						$publicitees[$nbrPubs.'_125X125']['url_site'] = Instances::$security->sortie($data['pub_url_site']);
						$publicitees[$nbrPubs.'_125X125']['url_image'] = Instances::$security->sortie($data['pub_url_image']);
						
						// -- On met le nombre d'affichages à jour.
						Instances::$db->sql_query('UPDATE site_publicites SET pub_nbr_affiche = pub_nbr_affiche-1 WHERE pub_id = "'.$data['pub_id'].'"');

					$nbrPubs++;
					}
		}
		
		// -- On s'occupe des banniére des 728X90px.
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_pubs_728 FROM site_publicites WHERE pub_valide = "1" AND pub_taille = "728X90" AND pub_nbr_affiche > 0'));
		if ($res['nbr_pubs_728'] > 0) {
			// -- Une petite condition pour savoir combien de pubs on va selectionner
			$selectNbrBan = ($res['nbr_pubs_728'] >= 2) ? $res['nbr_pubs_728'] : 2;
			
			// -- On effectue la requéte qui va chercher les publicités de 125X125px.
				$sql = 'SELECT pub_id, pub_taille, pub_url_site, pub_url_image, pub_nbr_affiche 
															FROM site_publicites 
															WHERE pub_valide = "1" AND pub_taille = "728X90" AND pub_nbr_affiche > 0
															LIMIT 0, '.$selectNbrBan.';';
				$res = Instances::$db->sql_query($sql);
					
					// -- Maintenant on remplit le tableau avec les données.
					$nbrPubs = 1;
					while ($data = mysql_fetch_array($res))
					{
						// -- On remplit le tableau.
						$publicitees[$nbrPubs.'_728X90']['url_site'] = Instances::$security->sortie($data['pub_url_site']);
						$publicitees[$nbrPubs.'_728X90']['url_image'] = Instances::$security->sortie($data['pub_url_image']);
						// -- On met le nombre d'affichages à jour.
						Instances::$db->sql_query('UPDATE site_publicites SET pub_nbr_affiche = pub_nbr_affiche-1 WHERE pub_id = "'.$data['pub_id'].'"');

					$nbrPubs++;
					}
		}

		// -- On s'occupe des banniére des 468X60px.
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_pubs_468 FROM site_publicites WHERE pub_valide = "1" AND pub_taille = "468X60" AND pub_nbr_affiche > 0'));
		if ($res['nbr_pubs_468'] > 0) {
			
			// -- On effectue la requéte qui va chercher les publicités de 125X125px.
				$sql = 'SELECT pub_id, pub_taille, pub_url_site, pub_url_image, pub_nbr_affiche 
															FROM site_publicites 
															WHERE pub_valide = "1" AND pub_taille = "468X60" AND pub_nbr_affiche > 0
															LIMIT 0, 1;';
				$res = Instances::$db->sql_query($sql);
					
					// -- Maintenant on remplit le tableau avec les données.
					while ($data = mysql_fetch_array($res))
					{
						// -- On remplit le tableau.
						$publicitees['1_468X60']['url_site'] = Instances::$security->sortie($data['pub_url_site']);
						$publicitees['1_468X60']['url_image'] = Instances::$security->sortie($data['pub_url_image']);
						// -- On met le nombre d'affichages à jour.
						Instances::$db->sql_query('UPDATE site_publicites SET pub_nbr_affiche = pub_nbr_affiche-1 WHERE pub_id = "'.$data['pub_id'].'"');
					}
		}
	// -- Pfiou on en a enfin fini la avec les pubs, on va maintenant passer à l'envois des mails.
        
			// -- On effectue la requéte qui va chercher les informations des membres.
				$sql = 'SELECT regis_id, regis_mail, regis_date_inscrit
															FROM site_inscrits 
															WHERE regis_site = "1" AND regis_validation = "";';
				$res = Instances::$db->sql_query($sql);
				
				// -- Le template du mail
				Instances::$tpl->set(array(
								'PUB' => $publicitees,
								'TIMESTAMP_IMG' => $new_timestamp,
				                'NEWS' => $this->data['textarea']));
				
				 while($data = mysql_fetch_array($res))
				 {
				 	Instances::$tpl->set('MBR', $data);
				  // -- On met le header en place
					$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
					$headers .= 'Reply-To: <mail@kdocode.com>'."\n"; // repondre à
					$headers .= 'Return-Path: <mail@kdocode.com>'."\n"; // Pour la réponse (la fonction répondre à l'expéditeur)
					//et Le renvoi du message s'il ne peut arriver au destinataire
					//$headers .= 'Cc: '."\n"; // destinataire en copy carbone
					//$headers .= 'Bcc: '."\n"; // destinataire en blind copy carbone
					//$headers .= 'Disposition-Notification-To: mail@kdocode.com'."\n"; // accuser de reception
					$headers .= 'MIME-Version: 1.0'."\n"; // version du mime
					$headers .= 'Content-Type: text/html; charset="utf-8"'."\n";
					// type de mail html
					$headers .= 'Content-Transfer-Encoding: 8bit'; // niveau d'encodage 7 oui 8 bit
					$headers .= 'To:  <'.Instances::$security->sortie($data['regis_mail']).'>'."\n"; // pour XXX
				  // -- On envoi le mail...
					mail(Instances::$security->sortie($data['regis_mail']), 'Tirage au sort sur KdoCode.com', Instances::$tpl->pparse('mails/participation.html'), $headers);
				 }
				 
			// -- Pour finir, on balance le message qui certifie que le mail a bien été envoyé.
			$this->setMsgPhp('Le mailing a bien été envoyé.', 'admin-accueil.html', 5, 0);
	}
	
    /**
     * Effectue un tirage au sort
     * 
     * @return bool
     * @access private
     */
    private function _makeTirage(){
        // -- On renseigne $this->data.
        $this->data = array(
                'code_allopass' => $_POST['code_allopass']
            );
		// -- Vérifie que le code allopass à bien été renseigné
	    if (empty($this->data['code_allopass'])){
			$this->setMsgPhp('Vous n\'avez pas renseigné de code allopass.', 'admin-tirage.html', 5, 1);
        }
		
		// -- Vérifie qu'il y ai des participants
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_participants FROM site_participations'));
		Instances::$tpl->set('NBR_PARTICIPANTS', $res['nbr_participants']);
	    if ($res[0] == 0){
			$this->setMsgPhp('Il n\'y a aucuns participants à ce tirage au sort.', 'admin-accueil.html', 5, 1);
        }
	
	// -- On va d'abord vérifier qu'il n'y ai pas déjà eu un mailing en cours.
		$monfichier = fopen(FICHIER_TIMESTAMP, 'a+');
		$timestamp = fgets($monfichier); // On lit la première ligne
		
			// -- Le fichier n'est pas vide, il y a donc un mailing en cours, pas deux mailing sans tirage.
			if (empty($timestamp)){
				$this->setMsgPhp('Vous ne pouvez pas lancer un tirage au sort si aucun mailing n\'a été envoyé.', 'admin-accueil.html', 5, 1);
			}
		fclose($monfichier); // On ferme le fichier.
		
		unlink(FICHIER_TIMESTAMP); // On supprime le fichier
		
			// -- Plus de vérification, donc on passe au tirage au sort.
        $sql = 'SELECT * FROM site_participations ORDER BY rand();';
        $res = Instances::$db->sql_query($sql);
		$data = mysql_fetch_array($res);
				
			// -- Requete qui va chercher toutes les informations sur l'inscrit.
		$sql = 'SELECT regis_id, regis_mail, regis_date_inscrit
											FROM site_inscrits 
											WHERE regis_id = "'.Instances::$security->entree($data['parti_id_inscrit']).'";';
		$res = Instances::$db->sql_query($sql);
		$data = mysql_fetch_array($res);
		
				//Headers des mails
				$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
				$headers .= 'Reply-To: <mail@kdocode.com>'."\n"; // repondre à
				$headers .= 'Return-Path: <mail@kdocode.com>'."\n"; // Pour la réponse (la fonction répondre à l'expéditeur)
				//et Le renvoi du message s'il ne peut arriver au destinataire
				$headers .= 'To:  <'.Instances::$security->sortie($data['regis_mail']).'>'."\n"; // pour XXX
				//$headers .= 'Cc: '."\n"; // destinataire en copy carbone
				//$headers .= 'Bcc: '."\n"; // destinataire en blind copy carbone
				//$headers .= 'Disposition-Notification-To: mail@kdocode.com'."\n"; // accuser de reception
				$headers .= 'MIME-Version: 1.0'."\n"; // version du mime
				$headers .= 'Content-Type: text/html; charset="utf-8"'."\n";
				// type de mail html
				$headers .= 'Content-Transfer-Encoding: 8bit'; // niveau d'encodage 7 oui 8 bit
				
				// -- Le template du mail
				Instances::$tpl->set('CODE_ALLOPASS' , $this->data['code_allopass']);
				
				// -- On envoi le mail au gagnant.
				mail(Instances::$security->sortie($data['regis_mail']), 'Vous avez gagne un code Allopass !', Instances::$tpl->pparse('mails/gagnant.html'), $headers);
			
				// -- Requete qui va vider la table.
		$sql = 'TRUNCATE TABLE site_participations;';
		$res = Instances::$db->sql_query($sql);
		
		// -- On insére une ligne dans le log qui montre qui a gagné.
		Instances::$security->addLog('l\'Id: '.Instances::$security->sortie($data['regis_id']).' ayant comme adresse email '.Instances::$security->sortie($data['regis_mail']).' a gagné un code allopass (Code Allopass: '.$this->data['code_allopass'].').', 'gagnants');

			
		// -- On balance le message.
		$this->setMsgPhp('Le tirage au sort a bien été effectué, l\'id '.Instances::$security->sortie($data['regis_id']).' ayant comme adresse email '.Instances::$security->sortie($data['regis_mail']).' a gagné un code allopass.', 'admin-accueil.html', 10, 0);
	}
	
} // end Frame_Child

?> 