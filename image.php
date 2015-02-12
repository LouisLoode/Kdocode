<?php
/**
 *  Index du site
 *
 * Génère l'image du code contenu dans le fichier caches/divers/code.ini
 *
 *
 *  @authors Loode
 */
 
function image($mot)
{
    $size = 32;
    $marge = 5;
    
    $box = imagettfbbox($size, 0, 'images/smartie.ttf', $mot);
    $largeur = $box[2] - $box[0];
    $hauteur = $box[1] - $box[7];
    
    
    $img = imagecreate($largeur+$marge*2, $hauteur+$marge*2);
    $blanc = imagecolorallocate($img, 255, 255, 255); 
    $noir = imagecolorallocate($img, 0, 0, 0);
    
    imagettftext($img, $size, 0,$marge,$hauteur+$marge, $noir, 'images/smartie.ttf', $mot);
    /*
    imageline($img, 2, $milieuHauteur + 8, $largeur - 2, $milieuHauteur + 8, $noir);
    imageline($img, 2,mt_rand(2,$hauteur), $largeur - 2, mt_rand(2,$hauteur), $noir);
    */

    
    imagepng($img);
    imagedestroy($img);
}

header("Content-type: image/png");

// -- Inclusion du fichier de configuration.
include 'includes/configuration.php';

$timestamp = $_GET['timestamp'];
if (!empty($timestamp))
{
	// -- On vérifie si le timestamp est numérique, si c'est pas le cas on arréte le chargement
	if (is_numeric($_GET['timestamp']))
	{	
		// -- On vérifie que le fichier du code n'est pas vie.
		$monfichier = fopen(DOSIER_CODES.$timestamp, 'a+');
		$code = fgets($monfichier); // On lit la première ligne
		fclose($monfichier); // On ferme le fichier
		if (!empty($timestamp))
		{
			image($code);
		}
	}
}


?>