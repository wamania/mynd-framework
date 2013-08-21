<?php

namespace Mynd\Core\Db;

/**
 * Classe d'accès à la BDD, utilise PDO
 *
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @package    Mynd Framework
 * @version    Release: @package_version@
 * @link       http://www.wamania.com
 */
class Db
{
    /**
     * Connexion DSN
     * @var String
     */
    private $dsn;

    /**
     * Connexion user
     * @var String
     */
    private $user;

    /**
     * Connexion pass
     * @var String
     */
    private $pass;

    /**
     * @var Object of PDO
     */
    private $pdo;

    /**
     * Default constructor
     * @param $config
     * @return unknown_type
     */
    public function __construct($dsn, $user, $pass)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Create PDO Object
     * @return void
     * @throw MfException
     */
    public function connect()
    {
        if ( (empty($this->dsn)) || (empty($this->user)) || (empty($this->pass)) ) {
            throw new MfException('You must give the access codes to the database');
        }

        try {
            $this->pdo = new \PDO($this->dsn, $this->user, $this->pass);

            $this->pdo->exec("SET NAMES 'utf8'");
            $this->pdo->exec("SET CHARACTER SET 'utf8'");

            $this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            $this->pdo->setAttribute( \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC );

        } catch(PDOExcetpion $e) {
            throw new LiException('Echec connexion PDO : '.$e->getMessage());
            //echo 'Echec connexion PDO : '.$e->getMessage()."\n<br />";
        }
    }

    public function __call($method, $args)
    {
        if (is_null($this->pdo)) {
            $this->connect();
        }

        if(method_exists($this->pdo, $method)) {
            return call_user_func_array(array($this->pdo, $method), $args);
        }
    }
}
