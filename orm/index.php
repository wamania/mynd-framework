<?php
/**
 * Fichier index.php
 * C'est lui qui devrait être inclu dans 
 * le script qui utilisera Lithium
 * Il contient l'initialisation, aisi que 2 classes nécessaires,
 * mais non codées ici : un cache et une classe d'exception
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

//require_once LI_MODEL.'db.php';
require_once LI_MODEL.'sqlquery.php';
require_once LI_MODEL.'model.php';
require_once LI_MODEL.'association.php';
require_once LI_MODEL.'associations/hasmany.php';
require_once LI_MODEL.'associations/hasone.php';
require_once LI_MODEL.'associations/belongsto.php';
require_once LI_MODEL.'associations/manytomany.php';

/**
 * Classe d'initialisation de lithium orm
 * 
 * Cette classe est appelée de l'extérieur afin d'initialiser le script
 * <code>
 * LiInitModel::init($path_to_models, array (
 *     'dsn' => 'mysql:host=localhost;dbname=lithium',
 *     'user' => 'root',
 *     'pass' => 'pass'
 * ));
 * </code>
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
class LiInitOrm 
{
    /**
     * Chemin vers les fichier "models"
     * @var String
     */
    private static $path_to_file;
    
    /**
     * @var Object of LiCache
     */
    public static $cache;
    
    /**
     * Initialisation et cofiguration
     * @return 
     * @param object $path
     * @param object $db
     */
    /*public static function init($path, $db) 
    {
        self::$path_to_file = $path;
        self::$cache = new LiCache();
        
        //LiDb::setConfig( $db );
    }*/
    
    /**
     * Appelée par l'autoLoad pour charger les models
     * @return void
     * @param String $class
     */
    public static function includeModel($class) 
    {
        $class_name = strtolower($class);
        
        if ( ! file_exists(LI_APP.'/model/'.$class_name.'.php')) {
            throw new LiException('File '.LI_APP.'/model/'.$class_name.'.php for model '.$class_name.' doesn\'t exist');
        }
        require_once LI_APP.'/model/'.$class_name.'.php';
    }
}


/**
 * Emulation d'un cache. A remplacer dans l'initialisation par un 
 * vrai system de cache. Le cache est utilisé pour stocker les 
 * columns d'une table et éviter de refaire la requete SHOW COLUMNS
 * à chaque nouvel objet. Ici il ne fait strictement rien, pour 
 * faciliter le déroulement des tests
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @package    Lithium
 * @subpackage ORM
 * @version    Release: @package_version@
 * @link       http://www.wamania.com
 * @since      Class available since Release 0.1
 */
class LiCache 
{
    
    private $data;
    
    public function get($key) 
    {
        return null;
    }
    
    public function set($key, $value) 
    {
    
    }
}


/**
 * Classe d'exception propre à Lithium
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
if (!class_exists('LiException')) {
    
    class LiException extends Exception {}
}


