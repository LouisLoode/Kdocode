<?php
/**
 * Page d'exemple
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	
	/*
	Si // est suivis de -*-, alors ce paramétre est obligatoire.
	*/
	
		// -- Vérifie si un membre est connecté, retirez le ! et vous vérifiez alors si le membre est déconnecté.
		if (!empty($_SESSION['membre'])){
            // Le membre est connecté
        }
		
		// -- Vérifie si le membre a le niveau d'accés requis pour entrer. On défini le niveau qu'il faut dans le paramétre de la session (0=bannis, 1=visiteur, 2=membre) plus d'infos dans la table des rangs.
		if (Instances::$security->checkAcces(1){
            // Le type a l'autorisation de rentrer ici.
        }
		
		// -*- On défini le template
        $this->setTpl('frames/index/portail.html');
		
		// -- On rajoute le nom d'un fichier css, on peut en mettre à l'infini.
        $this->addCSS('css1');
		$this->addCSS('css2');
		$this->addCSS('css3');
		$this->addCSS('css4');
		
		// -- Même histoire pour le js.
        $this->addJS('js1');
        $this->addJS('js2');
        $this->addJS('js3');
        $this->addJS('js4');
		
		// -- On défini le nom du titre, il ne peux y en avoir qu'un
        $this->setTitle('Index du site');
		
		// -- On défini la description de la page, unique elle aussi.
        $this->setDesc('Des news tout plein');
		
		// -- Message côté template, unique également ce message s'écrit dans un bloc div.
        $this->setMsgTpl('Vous n\'y êtes pas encore :)');
		
		// -- Message côté php, unique également car il appelle une fonction externe qui va afficher une page.
		//$this->setMsgPhp('Tchou, tchou !!!!!', 'index.html', 5, 1);
		
		//Appel de la méthode n°1
		$this->_firstMeth(); 
    }

	//Méthode 1 pour le test
    private function _firstMeth(){   
	echo 'leulll';
	// On appel la méthode 2
	$this->_secondMeth(); 
	}	
	
	//Méthode 2 pour le test
    private function _secondMeth(){   
	echo 'leulll2';
	}	
	
} // end Frame_Child

?> 