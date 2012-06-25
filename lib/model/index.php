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

//require_once LI_LIB.'model/db.php';
require_once LI_LIB.'model/model.php';
//require_once LI_LIB.'database/select.php';


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
class LiInitSimpleModel 
{
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