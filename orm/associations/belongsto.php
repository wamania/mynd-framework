<?php
/**
 * Fichier belongsto.php
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
 * Classe d'association BelongsTo
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
class LiAssociationBelongsTo extends LiAssociationAbstract 
{
    /**
     * On utilise une variable indépendante car si on tente d'accèder
     * à $this->results avant d'avoir enregistrerle parent, l'association
     * ira rechercher l'élément dans la base.
     * @var Bool
     */
    protected $delete;
    
    /**
     * Constructor
     * @return void
     * @param Object of LiModel $parent
     * @param Array $assoc
     */
    public function __construct($parent, $assoc) 
    {
        $this->delete = false;
        parent::__construct($parent, $assoc);
    }
    
    /**
     * Renvoie la valeur d'un des attributs de l'objet
     *
     * @param String $key
     * @return Mixed
     */
    public function __get($key) 
    {
        $object = $this->get();
        if (!is_null($object)) {
            return $object->$key;
        }
        
        return null;
    }
    
    /**
     * Assigne une valeur à un attribut de l'objet associé dans le cas
     * d'une relation 1..1
     *
     * @param String $key
     * @param Mixed $value
     * @return Bool
     */
    public function __set($key, $value) 
    {
        $object = $this->get();
        if ( !is_null($object) ) {
            $object->$key = $value;
        }
        return true;
    }
    
    /**
     * Setter de l'objet complet
     *
     * @param Object of LiModel $child
     */
    public function set(LiModel $child) 
    {
        $this->results[0] = $child;
    }
    
    /**
     * Getter de l'objet complet
     * 
     * @return object of LiModel
     */
    public function get() 
    {
        if (is_null($this->results)) {
            $this->execute();
        }
        if (isset($this->results[0])) {
            return $this->results[0];
        }
        return null;
    }
    
    /**
     * Save associated object
     * 
     * @return Bool
     */
    public function save() 
    {
        if ( (isset($this->results[0])) && (is_object($this->results[0])) ) {
            $this->results[0]->save();
        }/* else {
            $this->results[0]->delete();
        }*/
        
        return true;
    }
    
    /**
     * Indique que l'asociation sera rompue à l'enregistrement
     * 
     * @return void
     */
    public function delete()
    {
        $this->results = null;
        $this->delete = true;
    }
    
    /**
     * Confirme que la suppression de l'association a été demandée
     * 
     * @return Bool
     */
    public function isDeleted() {
        return $this->delete;
    }
    
    /**
     * Singleton qui charge aussi de nombreux paramètres
     * @return 
     * @param Object of LiModel $parent
     * @param Array $assoc
     */
    public static function load($parent, $assoc) 
    {
        $association = new LiAssociationBelongsTo($parent, $assoc);
        $oModel = new $assoc['class'];
        $table = $oModel->getTable();
        return $association->from($assoc['class'])->where($table.".".$assoc['foreign_key']."='".$parent->{$assoc['local_key']}."'");
    }
}
