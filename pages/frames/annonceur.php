<?php
/**
 * Page qui gére les annonceurs.
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	
// -- On utilise un switch afin de savoir quelle action utiliser (ici, on a le choix entre "accueil", "commander" et "acheter")
switch ($_GET['action']) { 

case "accueil": // -- Dans ce cas, on veux juste modifier le profil.

		// -- On défini le nom du titre.
        $this->setTitle('Page des Annonceurs');
		
		// -- On défini la description de la page.
        $this->setDesc('La page des Annonceurs vous permet d\'acheter des espace pub pour le jeu.');
	
		// -- On défini le template
        $this->setTpl('frames/annonceur/accueil.html');
		
		// -- On compte le nbr d'inscrit comme ça les membres viennent pas gueuler pour savoir le nombre d'inscrit qu'il y a. :p
		$res = mysql_fetch_array(Instances::$db->sql_query('SELECT COUNT(*) AS nbr_inscrits FROM site_inscrits'));
		Instances::$tpl->set('NBR_INSCRITS', $res['nbr_inscrits']);
		return;

break; // -- Fin de l'action "modifier".

case "commande": // -- Dans ce cas, on veux juste voir le profil.
		
		// -- Si pas de formulaire envoyé...
        if (!isset($_POST['contact_email'])){
			// -- Si le type est déjà entrain d'effectuer une commande et qu'il reviens sur cette page, ça veut dire qu'il veut changer des paramétres.
			if (!empty($_SESSION['commande'])) {
				// -- On supprime sa commande.
				session_destroy();
			}
			
			// -- On défini le nom du titre.
			$this->setTitle('Commander un espace publicitaire');
		
			// -- On défini la description de la page.
			$this->setDesc('La page de commande vous permet d\'acheter un espace publicitaire qui sera affiché sur les mails.');
	
			// -- On défini le template
			$this->setTpl('frames/annonceur/commande.html');
				
            return;
        }
		// -- Sinon, on traite les données
        $this->_checkPublicity();
        return;
		
break; // -- Fin de l'action "commande".

case "payement": // -- Dans ce cas, on va effectuer le payement

		// -- La session existe pas, donc on arrete l'Annonceur ici.
        if (empty($_SESSION['commande'])) {
			// -- Message d'erreur en PHP.
            $this->setMsgPhp('Vous n\'avez actuellement aucune commande, vous n\'avez donc rien à payer.', 'annonceur-commande.html', 5, 1);
        }
	// -- On vérifie par quel moyen en paye.
	if ($_SESSION['commande']['cout_total_pass'] == 1){// Allopass
			
			if(empty($_GET['RECALL'])){
			
			// -- On défini le nom du titre.
			$this->setTitle('Payement via Allopass');
		
			// -- On défini la description de la page.
			$this->setDesc('Sur cette page vous pouvez effectuer le payement de votre commande via Allopass.');
	
			// -- On défini le template
			$this->setTpl('frames/annonceur/allopass.html');
			
			// -- On crée les variables pour afficher le récapitulatif.
			Instances::$tpl->set('COMMANDE', $_SESSION['commande']);
			
			return;
			}
		// -- Sinon, on traite les données
        $this->_paymentAllopass();

	}else{// Paypal
				// -- Si le jeton est vide, on va lancer la procédure de payement afin de le créer.
				if(empty($_GET['token'])){
					$requete = construit_url_paypal();
					$requete = $requete.'&METHOD=SetExpressCheckout'.
						'&CANCELURL='.urlencode(URL_ANNULATION).
						'&RETURNURL='.urlencode(URL_RETOUR).
						'&AMT='.$_SESSION['commande']['cout_total_euros'].
						'&CURRENCYCODE=EUR'.
						'&DESC='.urlencode("Espace publicité sur KdoCode.").
						'&LOCALECODE=FR'.
						'&HDRIMG='.urlencode(LOGO_SITE);

				$ch = curl_init($requete);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$resultat_paypal = curl_exec($ch);

					if (!$resultat_paypal){ // S'il y a une erreur, on affiche "Erreur", suivi du détail de l'erreur.
						$this->setMsgPhp('Le payement via Paypal n\'a pas aboutis :<br />'.curl_error($ch).'', 'annonceur-payement.html', 5, 1);
					}
				
				$liste_param_paypal = recup_param_paypal($resultat_paypal); // Lance notre fonction qui dispatche le résultat obtenu en une array

					// Si la requête a été traitée sans succès
					if ($liste_param_paypal['ACK'] != 'Success'){
						// -- Message d'erreur
						$this->setMsgPhp('Erreur de communication avec le serveur PayPal.<br />'.$liste_param_paypal['L_SHORTMESSAGE0'].'<br />'.$liste_param_paypal['L_LONGMESSAGE0'].'', 'index.html', 5, 1);	
					}
				
					// Redirige le visiteur sur le site de paypal afin d'effectuer le payement.
					header('Location: '.SERVEUR_PAYPAL.$liste_param_paypal['TOKEN'].'');
				
					// -- On ferme la Connexion FTP vers Paypal.
					curl_close($ch);
				}
		// -- Sinon, on traite les données
        $this->_paymentPaypal();
	}


break; // -- Fin de l'action "payement".

default: // -- Par défaut, afin de ne prendre aucun risque on n'autorise pas aucun défaut à par la message d'erreur.
		$this->setMsgPhp('Vous n\'avez pas choisi d\'action à effectuer. Vous allez être automatiquement redirigé vers l\'accueil de l\'espace annonceurs.', 'annonceur-accueil.html', 5, 1);
}
} // -- Fin de main

    /**
     * Vérifie les informations d'enregistrement de publicité et les mets dans des sessions.
     * 
     * @return bool
     */
    private function _checkPublicity(){
	
        // -- On récupére le formulaire.
        $this->data = array(
                'contact_email' => $_POST['contact_email'],
                'format' => $_POST['format'],
                'url_banniere' => $_POST['url_banniere'],
                'site_web' => $_POST['site_web'],
                'nbr_affichage' => $_POST['nbr_affichage'],
				'reglement' => isset($_POST['reglement']),
            );
        
      // -- On vérifie tous les champs

        // -- Tout d'abord, on vérifie qu'aucun des champs ne sont vides.
        if (multi_empty(trim($this->data['contact_email']), trim($this->data['format']), trim($this->data['url_banniere']), trim($this->data['site_web']), trim($this->data['nbr_affichage']))){
			$this->setMsgPhp('Tout les champs sont obligatoires.', 'annonceur-commande.html', 5, 1);
        }
        
        // -- Vérification du mail.
        if (!(preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $this->data['contact_email']))){
			$this->setMsgPhp('L\'adresse email n\'est pas valide.', 'annonceur-commande.html', 5, 1);
        }
		
		// -- Le nombre d'affichage n'est pas valide.
        if (!(is_numeric($this->data['nbr_affichage'])) && $this->data['nbr_affichage'] == 0){
			$this->setMsgPhp('Le nombre d\'affichage n\'est pas valide, il ne doit evidement pas être inférieur à 1.', 'annonceur-commande.html', 5, 1);
        }
		
		// -- Aucun format n'a été selectionné.
        if ($this->data['format'] == 'aucun'){
			$this->setMsgPhp('Vous n\'avez selectionné aucun format.', 'annonceur-commande.html', 5, 1);
        }
		
		// -- Réglement refusé.
        if ($this->data['reglement'] == false){
			$this->setMsgPhp('Vous devez accepter le règlement pour pouvoir continuer.', 'annonceur-commande.html', 5, 1);
        }
        
		// -- On calcul le total que la personne va devoir payer en euros et en allopass.
		  // -- Calcul en euros.
			// -- Tableau des prix.
			$prixEuros = array('125X125' => 1.80,
							   '468X60' => 3.60,
						       '728X90' => 5.40);
			$prixUnitaire = $prixEuros[$this->data['format']];
			$coutTotalEuros = $prixEuros[$this->data['format']]*$this->data['nbr_affichage'];
			
		  // -- Calcul en allopass.
			// -- Tableau des allopass.
			$prixAllopass = array('125X125' => 1,
								  '468X60' => 2,
								  '728X90' => 3);
			$coutTotalPass = $prixAllopass[$this->data['format']]*$this->data['nbr_affichage'];
		
		// -- Il n'y a pas eu d'erreurs, donc on crée la session commande qui est un array qui réunit toutes les infos de la commande.
			$_SESSION['commande'] = array('contact_email' => $this->data['contact_email'],
										  'reference' => '',
										  'format' => $this->data['format'],
										  'nbr_affichage' => $this->data['nbr_affichage'],
										  'prix_unitaire' => $prixUnitaire,
										  'cout_total_euros' => $coutTotalEuros,
										  'cout_total_pass' => $coutTotalPass,
										  'url_banniere' => $this->data['url_banniere'],
										  'site_web' => $this->data['site_web']);
		
		// -- On crée les variables pour afficher le récapitulatif.
	    Instances::$tpl->set('COMMANDE', $_SESSION['commande']);
		
		// -- On défini le nom du titre.
        $this->setTitle('Récapitulatif');
		
		// -- On défini la description de la page.
        $this->setDesc('Afin de continuer, vous devez récapituler votre commande.');
	
		// -- On défini le template
        $this->setTpl('frames/annonceur/recapitulatif.html');
	return;
    }
	
	/**
     * Effectue le payement par allopass
     * 
     * @return bool
     */
    private function _paymentAllopass(){
	
		$RECALL = trim($_GET['RECALL']);
		
		// $RECALL contient le code d'accès
		$RECALL = urlencode($RECALL);

		// $AUTH doit contenir l'identifiant de VOTRE document
		$AUTH = urlencode(IDENTIFIANT_ALLOPASS);

		/**
		* envoi de la requête vers le serveur AlloPAss
		* dans la variable $r[0] on aura la réponse du serveur
		* dans la variable $r[1] on aura le code du pays d'appel de l'internaute
		* (FR,BE,UK,DE,CH,CA,LU,IT,ES,AT,...)
		* Dans le cas du multicode, on aura également $r[2],$r[3] etc...
		* contenant à chaque fois le résultat et le code pays.
		*/
		$r = @file("http://payment.allopass.com/api/checkcode.apu?code=$RECALL&auth=$AUTH");

		// on teste la réponse du serveur
		if(substr($r[0],0,2 ) != 'OK') 
		{
			// Le serveur a répondu ERR ou NOK : l'accès est donc refusé
			$this->setMsgPhp('Le serveur a mal répondu à votre commande.', 'annonceur-commande.html', 5, 1);
		}

		/**
		* Remplacez dans la ligne ci-dessus ".mondomaine.com" par le nom de domaine
		* de votre site!
		* Par exemple, si votre site est accessible à l'adresse :
		* http://perso.herbergeur.com/mapage
		* alors il faudra que vous mettiez ".herbergeur.com"
		* (n'oubliez pas le "." devant le nom de domaine !!)
		*/
		
		// -- On traite les données si il n'y a pas de soucis.
        $this->_registerPublicity();
		
    }
	

	/**
     * Effectue le payement par paypal
     * 
     * @return bool
     */
    private function _paymentPaypal(){
	
		$requete = construit_url_paypal(); // Construit les options de base

		// On ajoute le reste des options
		// La fonction urlencode permets d'encoder au format URL les espace, slash, deux point, etc.)
		$requete = $requete.'&METHOD=DoExpressCheckoutPayment'.
					'&TOKEN='.htmlentities($_GET['token'], ENT_QUOTES). // Ajoute le jeton qui nous a été renvoyé
					'&AMT='.htmlentities($_SESSION['commande']['cout_total_euros'], ENT_QUOTES).
					'&CURRENCYCODE=EUR'.
					'&PayerID='.htmlentities($_GET['PayerID'], ENT_QUOTES). // Ajoute l'identifiant du paiement qui nous a également été renvoyé
					'&PAYMENTACTION=sale';

		// Initialise notre session cURL. On lui donne la requête à exécuter.
		$ch = curl_init($requete);

		// Modifie l'option CURLOPT_SSL_VERIFYPEER afin d'ignorer la vérification du certificat SSL. Si cette option est à 1, une erreur affichera que la vérification du certificat SSL a échouée, et rien ne sera retourné. 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		// Retourne directement le transfert sous forme de chaîne de la valeur retournée par curl_exec() au lieu de l'afficher directement. 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// On lance l'exécution de la requête URL et on récupère le résultat dans une variable
		$resultat_paypal = curl_exec($ch);

		if (!$resultat_paypal){ // S'il y a une erreur, on affiche "Erreur", suivi du détail de l'erreur.
			$this->setMsgPhp('Le payement via Paypal n\'a pas aboutis :<br />'.curl_error($ch).'', 'annonceur-payement.html', 5, 1);
		}

		$liste_param_paypal = recup_param_paypal($resultat_paypal); // Lance notre fonction qui dispatche le résultat obtenu en une array

	
		// Si la requête n'a pas été traitée avec succès
		if ($liste_param_paypal['ACK'] != 'Success')
		{
			$this->setMsgPhp('Le payement via Paypal n\'a pas été effectué avec succés.', 'annonceur-payement.html', 5, 1);
		}
			
			// -- On traite les données si il n'y a pas de soucis.
        $this->_registerPublicity();
			
		// On ferme notre session cURL.
		curl_close($ch);
    }
	

	/**
     * Enregistre la publicité
     * 
     * @return bool
     */
    private function _registerPublicity(){
	
		// -- Plus de vérification, donc on passe à l'enregistrement dans la table.
        $sql = 'INSERT INTO site_publicites (pub_id, pub_contact, pub_taille, pub_url_site, pub_url_image, pub_nbr_affiche, pub_valide, pub_date_ajout)
                VALUES ("","'.Instances::$security->entree($_SESSION['commande']['contact_email']).'", "'.Instances::$security->entree($_SESSION['commande']['format']).'", "'.Instances::$security->entree($_SESSION['commande']['site_web']).'", "'.Instances::$security->entree($_SESSION['commande']['url_banniere']).'", "'.Instances::$security->entree($_SESSION['commande']['nbr_affichage']).'", "0", "'.time().'");';
        Instances::$db->sql_query($sql);
		
	// -- On récupére l'id de reference de la publicité.
		$_SESSION['commande']['reference'] = mysql_insert_id();
	
	// -- On va générer le récapitulatif et l'envoyer.
	    Instances::$tpl->set('COMMANDE', $_SESSION['commande']);
	
		//Headers du mail
		$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
		$headers .= 'To:  <'.$_SESSION['commande']['contact_email'].'>'."\n"; // pour XXX
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
        mail($_SESSION['commande']['contact_email'], 'Recapitulatif de votre achat Publicitaire', Instances::$tpl->pparse('mails/recapitulatif_annonceur.html'), $headers); 
	
	// -- On dit à l'annonceur qu'il a bien commandé son espace publicitaire.
	$this->setMsgPhp('Vous avez bien acheté votre espace publicitaire, un récapitulatif vous a été envoyé, nous vous conseillons de le garder précieusement. Votre publicité doit cependant être validée avant sa diffusion.', 'index.html', 10, 0);

    }
} // end Frame_Child


?>