<?php
/**
 *  Moteur de gestion de MySQL
 *
 * PAGE APPARTENANT AU NOYAU, NE PAS DISTRIBUER SANS AUTORISATION DU CREATEUR
 *
 *  @author Louis "Loode" DEBRAINE <louisdebraine@hotmail.com>
 *  @copyright ©Loode 2009

 */
 
class MySQL {
	
	    /**
     *    Si on est connecté à la BDD
     * 
     *    @acces private
     *    @var string
     */    
    private $connect = FALSE;

    /**
     *    Accés à la BDD
     * 
     *    @acces private
     *    @var string
     */    
    private $db = FALSE;
	
	/**
     *    Variable qui gére les erreurs
     * 
     *    @acces private
     *    @var string
     */    
    private $error = FALSE;
	
	/**
     *    Variable qui gére les résultats des requétes.
     * 
     *    @acces public
     *    @var string
     */    
    public $result = FALSE;

    /**
     *    Nombre de requétes
     * 
     *    @acces public
     *    @var string
     */    
    public $nbrQueries = 0;
	
	
	    /**
     * Connexion à la base de donnée
     *
     * @param string $host --> Serveur MySQL
     * @param string $user --> Utilisateur de la BDD
     * @param string $pass --> Mot de passe
     * @param string $db --> Base de donnée
     * @access public
     * @return true
     */
    public function __construct($host,$user,$pass,$db)
        {
        $this->connect = mysql_connect($host,$user,$pass); // On se connecte au serveur.
        if ($this->connect != FALSE) // Si la connexion n'a pas foiré, on passe à la selection de la Base de donnée.
            {
            $this->db = mysql_select_db($db,$this->connect); // On selectionne la BDD.

			//$this->sql_query('SET NAMES utf8'); // On demande au serveur d'envoyer de traiter les données en utf-8.

            if ($this->db == FALSE) // Si la selection de la BDD foire on envoit un message d'erreur et on ferme la connexion au serveur.
                {
                mysql_close($this->connect); // Fermeture de la connexion.
                $this->connect = FALSE;
                exit('<p><strong>MySQL->MySQL ::</strong> Impossible de séléctionner une base de donnée.</p>');
                }
            return TRUE;
            }
        exit('<p><strong>MySQL->MySQL ::</strong> Impossible de se connecter à la base de donnée ' .$db. '.</p>');
        }


		/**
     * Changement de Base de donnée
     *
     * @param string $db --> Base de donnée
     * @access public
     * @return true
     */
    public function sql_change_db($db)
        {
        if ($this->connect != FALSE) // Si la connexion n'a pas foiré.
            {
            $this->db = mysql_select_db($db,$this->connect); // Selection de la nouvelle BDD.
            if ($this->db == FALSE) // Si la selection est ok, on ferme la connexion à l'ancienne BDD.
                {
                mysql_close($this->connect); // Fermeture de la connexion.
                $this->connect = FALSE;
                exit('<p><strong>MySQL->sql_change_db ::</strong> Impossible de séléctionner une base de donnée.</p>');
                }
            return TRUE;
            }
        return FALSE;
        }


		/**
     * Envois d'une requéte
     *
     * @param string $query --> La requéte SQL à envoyer
     * @param string $db --> Base de donnée
     * @access public
     * @return true
     */
    public function sql_query($query)
        {
        $this->query = $query; // On change de variable, question pratique.
        if (!empty($this->query) AND $this->connect != FALSE) // Vérification pour savoir si le requéte est remplie ou non.
            {
            $this->result = mysql_query($this->query,$this->connect); // On envoit la requéte.
            $this->nbrQueries++; // On rajoute 1 au nombre de requétes.
            $this->error = ($this->result == FALSE) ? TRUE : FALSE; // Vérification du plantage de la requétes
            $this->result = ($this->error) ? $this->query."\n".mysql_errno($this->connect).' : '.mysql_error($this->connect) : $this->result; // Si il y a une erreur,  on remplace le résultat de la requéte par l'erreur.
             if ($this->error) die($this->result); 
            return $this->result;
            }
        $this->error = TRUE;
        $this->result = '<p><strong>MySQL->sql_query ::</strong> La requéte SQL est vide.</p>';
        exit($this->result);
		}


		/**
     * Fermeture de la connexion
     *
     * @access public
     * @return $this->connect;
     */
	public function sql_close()
        {
        if ($this->connect != FALSE) // Si on est connecté.
            {
            $this->connect = mysql_close($this->connect); // On se déconnecte du serveur.
            return $this->connect;
            }
        return FALSE;
        }
    }
?>