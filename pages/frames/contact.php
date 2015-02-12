<?php
/**
 * Page de contact
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
        $this->setTpl('frames/contact/index.html');
		
		// -- On défini le nom du titre, il ne peux y en avoir qu'un
        $this->setTitle('Page de Contacts');
		
		// -- On défini la description de la page, unique elle aussi.
        $this->setDesc('Page contenant tout les mails de contacts qui servent à joindre l\'équipe.');
    }
	
} // end Frame_Child

?> 