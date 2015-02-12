<?php
/**
 * Page de présentation du contrat
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
		
		// -*- On défini le template
        $this->setTpl('frames/contrat/accueil.html');
		
		// -- On défini le nom du titre, il ne peux y en avoir qu'un
        $this->setTitle('Contrat pour l\'achat des Espaces Publicitaires');
		
		// -- On défini la description de la page, unique elle aussi.
        $this->setDesc('Présentation du Contrat pour l\'achat des Espaces Publicitaires.');
    }
	
} // end Frame_Child

?> 