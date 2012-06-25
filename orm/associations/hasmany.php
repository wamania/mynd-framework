<?php
/**
 * Fichier hasmany.php
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
 * Classe d'association HasMany
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
class MfAssociationHasMany extends MfAssociationAbstract 
{
    /**
     * Tableau des objects à ajouter
     * @var Array
     */
    protected $data_add;
    
    /**
     * Tableau des objects à supprimer
     * @var Array
     */
    private $data_del;
    
    /**
     * Doit-on tout supprimer
     * @var Bool
     */
    private $deleteAll;
    
    /**
     * Foreign Key
     * @var String
     */
    private $fk;
    
    /**
     * Constructor
     * @return void
     * @param object of LiModel $parent
     * @param Array $assoc
     */
    public function __construct($parent, $assoc) 
    {
        $this->data_add = array();
        $this->data_del = array();
        $this->fk = 0;
        $this->deleteAll = false;
        
        parent::__construct($parent, $assoc);
    }
    
    /**
     * Ajoute un object à cette association
     * On vérifie avant que l'objet ne faire pas
     * déjà parti de l'association
     * 
     * @return 
     * @param object of LiModel $child
     */
    public function add(LiModel $child) 
    {
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
            // Pour l'affichage, en commentaire car en fait, on ne met 
            // rien dans les résultats tant qu'on a pas enregistré
            //$this->results[] = $child;
            
            // Pour l'enregistrement
            $this->data_add[] = $child;
        }
    }

    /**
     * Efface un objet
     * N'est effectif qu'à l'enregistrement
     *
     * @param Object of LiModel $child
     * @return void
     */
    public function delete(LiModel $child) 
    {	
        $this->data_del[] = $child;
    }
    
    /**
     * Efface tous les objects
     * N'est effectif qu'à l'enregistrement
     * 
     * @return void
     */
    public function deleteAll() 
    {
        $this->deleteAll = true;
    }

    /**
     * Setter de la valeur de la clé étrangère
     *
     * @param Int $fk_value
     */
    public function setFk($fk) 
    {
        $this->fk = $fk;
    }
    
    /**
     * Enregistrement
     * 
     * @return bool
     */
    public function save() 
    {
        if ($this->deleteAll) {
            $className = $this->assoc['class'];
            $oModel = new $className;
            $table = $oModel->getTable();
            $this->db->query("DELETE FROM ".$table." WHERE ".$this->parent->getTable()."_id='".$this->parent->id."'");
        
        } else {
            // Delete
            foreach ($this->data_del as $key => $data) {
                $data->delete();
                $data->save();
            }
        }
        
        // Add
        foreach ($this->data_add as $key => $data) {
            
            $this->data_add[$key]->{$this->assoc['foreign_key']} = $this->fk;
            $this->data_add[$key]->save();
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
        $association = new LiAssociationHasMany($parent, $assoc);
        return $association->from($assoc['class'])->where($assoc['foreign_key']/*$parent->getTable()."_".$assoc['local_key']*/."='".$parent->{$assoc['local_key']}."'");
    }
}
