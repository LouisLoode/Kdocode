<?php
/**
 * Page de test
 *
 * @package Kdocode.com
 * @author Louis Debraine (louisdebraine@hotmail.com)
 * @copyright ©Loode
 * @create 29/10/2009, Loode
 */

final class Frame_Child extends Frame {    
    
	// Frame main obligatoire, c'est elle qui va gérer toute la frame.
    protected function main(){
	

		$this->setMsgPhp('L\'adresse email entrée n\'a pas le bon format.', 'index.html', 5, 0);
		
		}
	
} // end Frame_Child

?> 