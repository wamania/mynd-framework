<?php
/**
 * Fichier manytomany.php
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
 * Classe d'association ManyToMany
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
class LiAssociationManyToMany extends LiAssociationAbstract 
{
    protected $data_add;
    
    private $data_del;
    
    private $deleteAll;
    
    private $fk;
    
    public function __construct($parent, $assoc) 
    {
        $this->data_add = array();
        $this->data_del = array();
        $this->fk = 0;
        $this->deleteAll = false;
        
        parent::__construct($parent, $assoc);
    }
    
    /**
     * On commence par remplir le tableau des valeurs existantes dans
     * la base, afin que l'on ne croit pas que l'objet en paramètre
     * soit le seul de cette association.
     * 
     * @return 
     * @param object $child
     */
    public function add(LiModel $child) 
    {
        //print_r($child);
        if ( (is_null($this->results)) && (isset($this->parent->id)) ) {
            $this->execute();
        }
        
        $already_exists = false;
        if ( (!is_null($this->results)) && (!$child->isNew()) ) {
            foreach ($this->results as $data) {
                if ($data->id == $child->id) {
                    $already_exists = true;
                    continue;
                }
            }
        }

        if (!$already_exists) {
            // Pour l'affichage
            $this->results[] = $child;
            
            // Pour l'enregistrement
            $this->data_add[] = $child;
        }
    }
    
    /**
     * Effacer un objet dans le cas d'une relation 1..n
     *
     * @param Object of yModel $child
     */
    public function delete(LiModel $child) 
    {
        $this->data_del[] = $child;
    }
    
    public function deleteAll() 
    {
        $this->deleteAll = true;
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
    
    public function save() 
    {
        // Table de liaison
        $className = $this->assoc['class'];
        $oModel = new $className;
        $assoc_table = $oModel->getTable();
        
        if ($assoc_table > $this->parent->getTable()) {
            $jointable = $this->parent->getTable().'_'.$assoc_table;
        } else {
            $jointable = $assoc_table.'_'.$this->parent->getTable();
        }
        
        if ($this->deleteAll) {
            $this->db->query("DELETE FROM $jointable WHERE ({$this->parent->getTable()}_".$this->assoc['local_key']."={$this->fk})");
        }
        
        // Delete
        foreach ($this->data_del as $key => $data) {
            $this->db->query("DELETE FROM $jointable WHERE ({$this->parent->getTable()}_".$this->assoc['local_key']."={$this->fk}) AND ({$data->getTable()}_".$this->assoc['foreign_key']."=".$data->{$this->assoc['foreign_key']}.")");
        }
        
        // Ajout
        foreach ($this->data_add as $data) {
            $data->save();
            $this->db->query("INSERT INTO $jointable ({$this->parent->getTable()}_".$this->assoc['local_key'].", {$data->getTable()}_".$this->assoc['foreign_key'].") VALUES ({$this->fk}, ".$data->{$this->assoc['foreign_key']}.")");
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
        // on cherche la clé correspondant à la classe à joindre
        $classname = $assoc['class'];
        $model = new $classname;
        $associations = $model->getAssociations();
        foreach ($associations as $key => $a) {
            if ($a['class'] == get_class($parent)) {
                $assoc_key = $key;
            }
        }
        
        $association = new LiAssociationManyToMany($parent, $assoc);
        return $association->from($assoc['class'])->join($assoc_key)->where($parent->getTable().".".$assoc['local_key']."='".$parent->{$assoc['local_key']}."'");
    }
}
