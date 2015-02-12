<?php
	//Headers du mail
		$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
		$headers .= 'To:  <julienj.brun@gmail.com>'."\n"; // pour XXX
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
        mail('julienj.brun@gmail.com', 'Votre inscription sur KDOCode.com', 'test', $headers); 
		
		//Headers du mail
		$headers = 'From: "KDOCode.com" <mail@kdocode.com>'."\n"; // de
		$headers .= 'To:  <louisdebraine@hotmail.com>'."\n"; // pour XXX
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
        mail('louisdebraine@hotmail.com', 'Votre inscription sur KDOCode.com', 'test', $headers); 
?>