<?php
if (get_magic_quotes_gpc() == 1)
{
	function remove_magic_quotes_gpc(&$value) {
	
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'remove_magic_quotes_gpc');
	array_walk_recursive($_POST, 'remove_magic_quotes_gpc');
	array_walk_recursive($_COOKIE, 'remove_magic_quotes_gpc');
}

function code_alea(){
	$string = strtoupper(uniqid());
	return substr($string,5,strlen($string));
}

function chiffre_alea(){
	$string = mt_rand(111,999);
	return $string;
}

function supr_espaces($string){
	$string = str_replace(CHR(32),"",$string); 
	return $string;
}

function message($msg, $redirect, $tps=5, $type=0)
{
		// On gére les types de messages
		$class = array('confirm', 'error', 'neutral'); // pour le css.
		$title = array('Message de confirmation', 'Message d\'erreur', 'Message d\'information'); //Pour le titre de la page
		
            Instances::$tpl->set(array(
									'TITLE' => $title[$type],
									'CLASS_CSS' => 'message_' . $class[$type],
									'TEMPS' => $tps,
									'MESSAGE' => $msg,
									'REDIRECTION' => $redirect
									));
        

		 // On parse la page afin de l'affichée.
		Instances::$tpl->parse('messages.html');
}


function multi_empty(){
    // -- Pas de parametre.. C'est donc vrai :o
    if (func_num_args() == 0){
        return true;
    }
    
    $args = func_get_args();
    
    // -- On teste chacun des arguments. Si y'en a un vide, on retourne true..
    foreach ($args as $arg){
        if (empty($arg)){
            return true;
        }
    }
    
    return false;
}
function rand_captcha(){
    $ary = array(
            'a' => array(
                    'nom' => '',
                    'valeur' => 0
                ),
            
            'b' => array(
                    'nom' => '',
                    'valeur' => 0
                ),
            
            'operateur' => '',
            'solution' => 0
        );
    
    // -- Les chiffres possibles.
    $elements = array(
            'un' => 1,
            'deux' => 2,
            'trois' => 3,
            'quatre' => 4,
            'cinq' => 5,
            'six' => 6,
            'sept' => 7,
            'huit' => 8,
            'neuf' => 9,
            'dix' => 10
        );
    
    // -- Tirage au hasard des deux nombres à utiliser
    $ary['a']['nom'] = array_rand($elements);
    $ary['b']['nom'] = array_rand($elements);
    
    $ary['a']['valeur'] = $elements[$ary['a']['nom']];
    $ary['b']['valeur'] = $elements[$ary['b']['nom']];
    
    // -- Opérations possibles + calcul des solutions.
    $ops = array(
            'plus' => $ary['a']['valeur'] + $ary['b']['valeur'],
            'moins' => $ary['a']['valeur'] - $ary['b']['valeur'],
            'fois' => $ary['a']['valeur'] * $ary['b']['valeur']
        );
    
    $ary['operateur'] = array_rand($ops);
    $ary['solution'] = $ops[$ary['operateur']];
    
    // -- Si on effectue une soustraction, avec $b > $a, alors on inverse a et b.. et on adapte la solution.
    if ($ary['operateur'] == 'moins' && ($ary['b']['valeur'] > $ary['a']['valeur'])){
        $c = $ary['b'];
        $ary['b'] = $ary['a'];
        $ary['a'] = $c;
        
        $ary['solution'] *= -1; 
    }
    
    // -- On épure l'array.
    $ary['a'] = $ary['a']['nom'] . ' ';
    $ary['b'] = $ary['b']['nom'] . ' ';
    $ary['operateur'] .= ' ';
    $ary['captcha'] = trim($ary['a'] . $ary['operateur'] . $ary['b']);
    
    // -- on retourne le tout :)
    return $ary;
}


  function construit_url_paypal()
  {

	$api_paypal = API_PAYPAL.'VERSION='.VERSION_API_PAYPAL.'&USER='.USER_PAYPAL.'&PWD='.PASS_PAYPAL.'&SIGNATURE='.SIGNATURE_PAYPAL; // Ajoute tous les paramètres

	return 	$api_paypal; // Renvoie la chaine contenant tous nos paramètres.
  }

  function recup_param_paypal($resultat_paypal)
  {
	$liste_parametres = explode("&",$resultat_paypal); // Créé un tableau de paramètres
	foreach($liste_parametres as $param_paypal) // Pour chaque paramètre
	{
		list($nom, $valeure) = explode("=", $param_paypal); // Sépare le nom et la valeur
		$liste_param_paypal[$nom]=urldecode($valeure); // Créé l'array finale
	}
	return $liste_param_paypal; // retourne l'array
  }

?>