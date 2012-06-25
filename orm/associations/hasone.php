<?php
/**
 * Fichier hasone.php
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
 * Classe d'association HasOne
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
class MfAssociationHasOne extends MfAssociationAbstract 
{
    
    private $fk;
    
    private $delete;
    
    private $object_add;
    
    public function __construct($parent, $assoc) 
    {
        
        $this->fk = 0;
        $this->delete = false;
        $this->object_add = null;
        
        parent::__construct($parent, $assoc);
    }
    
    public function exists() 
    {
        if (is_null($this->results)) {
            $this->execute();
        }
        return isset($this->results[0]);
    }
    
    /**
     * Renvoie la valeur d'un des attributs de l'objet dans le cas
     * d'une relation 0..1
     *
     * @param String $key
     * @return Mixed
     */
    public function __get($key) 
    {
        if (is_null($this->results)) {
           $this->execute();
        }
        if ( (isset($this->results[0])) && (is_object($this->results[0])) ) {
            return $this->results[0]->$key;
        }
        
        return null;
    }
    
    /**
     * Getter de l'objet complet
     *
     */
    public function get() 
    {
        if (is_null($this->results)) {
            $this->execute();
        }
        return $this->results[0];
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
        if ( (isset($this->results[0])) && (is_object($this->results[0])) ) {
            $this->results[0]->$key = $value;
        }
        return true;
    }
    
    /**
     * Setter de l'objet complet
     *
     * @param Object of yModel $child
     */
    public function set(LiModel $child) 
    {
        $this->results[0] = $child;
    }
    
    /**
     * Setter de la valeur dela clé étrangère dans le
     * cas d'une relation 1..n
     *
     * @param Int $fk_value
     */
    public function setFk($fk) 
    {
        $this->fk = $fk;
    }
    
    public function delete() 
    {
        $this->results = null;
        $this->delete = true;
    }
    
    public function save() 
    {
        if ($this->delete) {
            $className = $this->assoc['class'];
            $oModel = new $className;
            $table = $oModel->getTable();
            $this->db->query("DELETE FROM ".$table." WHERE ".$this->parent->getTable()."_".$this->assoc['local_key']."='".$this->parent->{$this->assoc['local_key']}."'");
            return true;
        }
        if (!empty($this->results)) {
            $fk_name = $this->parent->getTable().'_id';
            $this->results[0]->$fk_name = $this->fk;
            $this->results[0]->save();
        }
        
        return true;
    }
    
    /**
     * Singleton qui charge aussi de nombreux paramètres
     * @return 
     * @param Object of LiModel $parent
     * @param Array $assoc
     */
    public static function load($parent, $assoc) 
    {
        $association = new LiAssociationHasOne($parent, $assoc);
        return $association->from($assoc['class'])->where($parent->getTable()."_".$assoc['local_key']."='".$parent->{$assoc['local_key']}."'");
    }
}
