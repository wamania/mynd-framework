<?php
/**
 * Fichier db.php
 *
 * LICENCE
 *     You are free:
 *       to Share - to copy, distribute and transmit the work
 *       to Remix - to adapt the work
 *     Under the following conditions:
 *       Attribution - You must attribute the work in the manner specified by the author or licensor
 *       (but not in any way that suggests that they endorse you or your use of the work).
 *       Just keep this header.
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @version    $Id:$
 * @link       http://www.wamania.com
 * @since      File available since Release 0.1
 */

/**
 * Classe d'accès à la BDD, utilise PDO
 *
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @package    Lithium
 * @subpackage ORM
 * @version    Release: @package_version@
 * @link       http://www.wamania.com
 * @since      Class available since Release 0.1
 */
class LiDb
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
	 * @var Object of PDOStatement
	 */
	//private $pdoStatement;

	/**
	 * Default constructor
	 * @param $config
	 * @return unknown_type
	 */
	public function __construct($dsn, $user, $pass)
	{
		/*if ( ! is_null($config)) {
		 $this->setConfig($config);
		 }*/
		$this->dsn = $dsn;
		$this->user = $user;
		$this->pass = $pass;
	}

	/**
	 * Set connexion config
	 * @return void
	 * @param Array $config
	 */
	/*public function setConfig($config)
	 {
	 $this->dsn = $config['dsn'];
	 $this->user = $config['user'];
	 $this->pass = $config['pass'];
	 }*/

	/**
	 * Create PDO Object
	 * @return void
	 * @throw LiException
	 */
	public function connect()
	{
		if ( (empty($this->dsn)) || (empty($this->user)) || (empty($this->pass)) ) {
			throw new LiException('You must give the access codes to the database');
		}

		try {
			$this->pdo = new PDO($this->dsn, $this->user, $this->pass);

			$this->pdo->exec("SET NAMES 'utf8'");
			$this->pdo->exec("SET CHARACTER SET 'utf8'");

			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );

		} catch(PDOExcetpion $e) {
			//throw new LiException($e->getMessage());
			echo 'Echec connexion PDO : '.$e->getMessage()."\n<br />";
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

	/**
	 * Destroy PDO Object
	 * @return void
	 */
	/*public function disconnect()
	 {
	 $this->pdo = null;
	 }*/

	/**
	 * @deprecated
	 * Enter description here ...
	 */
	public function &getPdo()
	{
		if ($this->pdo === null) {
			$this->connect();
		}
		return $this->pdo;
	}
}
