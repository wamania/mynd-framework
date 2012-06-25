<?php
/**
 * Fichier model.php
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
 * Implémentation du design pattern Active record.
 * 
 * Tous les modèles créés hériteront de cette classe.
 * Elle contient les méthodes d'accès aux attributs, ainsi
 * que l'enregistrement des données.
 * Deux méthodes static utilisent la classe LiSqlQuery 
 * et agissent ainsi comme une factory d'objet "Model"
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
class LiModel 
{
    /**
     * Tableau contenant les données
     
     * @var Array
     */
    private $data;
    
    /**
     * Objet de connexion à la bdd
     
     * @var Object of LiDb
     */
    protected $db;
    
    /**
     * true si les attributs ont changé.
     * Utilisé pour savoir si sauvegarder ou pas
     
     * @var Bool
     */
    private $hasChanged;
    
    /**
     * Description des colonnes acquis directement sur la table Mysql
     *
     * @var Array
     */
    protected $associations;
    
    /**
     * Le nom de la table liée à cette classe
     * 
     * @var String
     */
    protected $table;
    
    /**
     * Flag pour savoir si on efface le tuple courant
     * 
     * @var Bool
     */
    private $delete;
    
    /**
     * Cache pour le query column
     * @var unknown_type
     */
    protected static $cache;
    
    /**
     * Méthode static de récupération d'un objet unique
     * 
     * @return Object of LiModel
     * @param $class Object
     * @param $where String
     * @param $params Mixed
     */
    public static function get($where, $params = array()) 
    {
    	$class = get_called_class();
    	
        $sqlquery = new LiSqlQuery();
        $results = $sqlquery->from($class);
        if (is_numeric($where)) {
            $results = $results->where("id=".$where);
        } else {
            if (!is_array($params)) {
                $params = array($params);
            }
            $results = $results->where($where, $params);
        }
        $current = $results->current();
        if (empty($current)) {
            return null;
        }
        return $current;
    }
    
    /**
     * Méthode static de récupération d'un objet sqlquery
     * qui servira à effectuer une requete SELECT retournant
     * plusieurs objets
     * 
     * @return Object of LiSqlQuery
     * @param $class String
     */
    public static function find($where=null, $params=null) 
    {
    	$class = get_called_class();
    	
        $sqlquery = new LiSqlQuery();
        $sqlquery =  $sqlquery->from($class);
        if (!empty($where)) {
            $sqlquery = $sqlquery->where($where, $params);
        }
        return $sqlquery;
    }
    
    /**
     * Main constructor
     * 
     * @return void
     * @param $params Array[optional]
     */
    public function __construct($params=array()) 
    {
        $this->data = array();
        $this->id = null;
        $this->delete = false;

        if (!is_array($this->associations)) {
            $this->associations = array();
        }
        
        // L'objet DB
        $this->db = _r('db');//LiDb::getSingleton();
        
        // Check has_changed apres l'initialisation
        $this->hasChanged = false;
        
        // On rempli les vides dans le tableau $associations
        if (!empty($this->associations)) {
            foreach ($this->associations as $key => $assoc) {
                if (isset($assoc['type'])) {
                    
                    if (empty($assoc['class'])) {
                        $this->associations[$key]['class'] = self::tableToClass($key);
                    }
                    if (empty($assoc['local_key'])) {
                        switch($assoc['type']) {
                            case 'hasmany':
                            case 'hasone':
                            case 'manytomany':
                                $this->associations[$key]['local_key'] = 'id';
                                break;
                            
                            case 'belongsto':
                                $this->associations[$key]['local_key'] = $key.'_id';
                                break;
                        }
                    }
                    if (empty($assoc['foreign_key'])) {
                        switch($assoc['type']) {
                            case 'hasmany':
                            case 'hasone':
                                $this->associations[$key]['foreign_key'] = $this->getTable().'_id';
                                break;
                            
                            case 'belongsto':
                            case 'manytomany':
                                $this->associations[$key]['foreign_key'] = 'id';
                                break;
                        }
                    }
                }
            }
        }

        // Remplissage
        if (!empty($params)) {
            $this->fromArray($params);
        }
    }
    
    /**
     * Remplie l'objet à partir d'un tableau
     * 
     * @return Void
     * @param Array $array
     */
    public function fromArray($array) 
    {
        foreach ($array as $key => $el) {
            if ( (!is_object($el)) && (!is_array($el)) ) {
                $this->$key = $el;
            } elseif (array_key_exists($key, $this->associations)) {
                if (is_array($el)) {
                    foreach ($el as $inEl) {
                        $this->$key->push($inEl);
                    }
                } else {
                    $this->$key->push($el);
                }
            }
        }
    }

    /**
     * Magic Getter
     
     * @return mixed
     * @param $key String
     */
    public function __get($key) 
    {
        $columns = $this->queryColumns();
        
        if (isset($this->data[$key])) {
            return $this->data[$key];
            
        } elseif (array_key_exists($key, $this->associations)) {
            $this->data[$key] = LiAssociationAbstract::load($this, $this->associations[$key]);
            return $this->data[$key];
            
        } elseif (!array_key_exists($key, $columns)) {
            throw new Exception('L\'attribut '.$key.' n\'est ni une valeur, ni une association pour le model '.$this->getTable());
            
        }
        return null;
    }
    
    /**
     * Magic Isset
     * 
     * @return Bool
     * @param String $key
     */
    public function __isset($key) 
    {
        if (isset($this->data[$key])) {
            return true;
        }
        return false;
    }
    
    /**
     * Magic Setter
     * 
     * @return void
     * @param $key String
     * @param $value Mixed
     */
    public function __set($key, $value) 
    {
        if (is_object($value)) {
            throw new Exception('Vous devez utiliser les méthodes add() ou set() pour assigner des objets');
        }
        $this->hasChanged = true;
        $this->data[$key] = $value;
    }
    
    /**
     * Magic unset
     * 
     * @return void
     * @param $key String
     */
    public function __unset($key) 
    {
        unset($this->data[$key]);
    }
    
    /**
     * Save current object in database
     * @return Bool
     */
    public function save() 
    {
        // effacement
        if (!empty($this->data['id']) && (is_numeric($this->data['id'])) && ($this->delete)) {
            $this->db->query("DELETE FROM " . $this->getTable() . " WHERE id=" . $this->data['id']);
            $this->data = array();
            $this->delete = false;
            return true;
        }
        
        // Récupération des colonnes
        $columns = $this->queryColumns();
        
        // Gestion des associations : BELONGSTO
        foreach ($this->associations as $key => $assoc) {
            if ($assoc['type'] == 'belongsto') {
                if (isset($this->data[$key])) {
                    if (($this->data[$key]->isDeleted())) {
                        $this->data[$assoc['local_key']] = null;
                    
                    } elseif ($this->data[$key]->save()) {
                        
                        // En relation 1..1, si on change, alors il faut répercuter.
                        if (!isset($this->data[$assoc['local_key']])) {
                            $this->data[$assoc['local_key']] = null;
                        }
                        $this->hasChanged = false;
                        
                        if ($this->data[$assoc['local_key']] != $this->data[$key]->{$assoc['foreign_key']}) {

                            $this->data[$assoc['local_key']] = $this->data[$key]->{$assoc['foreign_key']};
                            $this->hasChanged = true;
                        }

                        // On invalide les anciens résultats pour forcer une
                        // nouvelle requete avec ces nouveaux objets
                        unset($this->data[$key]);
                    }
                }
            }
        }

        // Update
        if (!empty($this->data['id']) && (is_numeric($this->data['id'])) && ($this->hasChanged)) {
            
            $query = "UPDATE " . $this->getTable() . " SET ";
            unset($columns['id']);

            $tabquery = array();
            $tabParams = array();
            foreach ($columns as $field) {
                if (isset($this->data[$field[0]])) {
                    $tabquery[] = $field[0]."=?";
                    $tabParams[] = $this->data[$field[0]];
                }
            }
            $query .= implode(', ', $tabquery);
            $query .= " WHERE id=" . $this->data['id'];
            
            $s = $this->db->prepare($query);
            $s->execute($tabParams);
        
        // create
        } elseif (empty($this->data['id'])) {
            $query = "INSERT INTO " . $this->getTable() . " (";
            $tabkeys = array();
            $tabvalues = array();
            $tabparams = array();
            unset($columns['id']);
            foreach ($columns as $field) {
                if (isset($this->data[$field[0]])) {
                    $tabkeys[] = $field[0];
                    $tabvalues[] = '?';
                    $tabparams[] = $this->data[$field[0]];
                }
            }
            $query .= implode(',', $tabkeys) . ") VALUES (" . implode(',', $tabvalues) . ")";

            $s = $this->db->prepare($query);
            $s->execute($tabparams);
            $this->data['id'] = $this->db->lastInsertId();
        }

        // Gestion des associations
        foreach ($this->associations as $key => $assoc) {
            if ($assoc['type'] == 'hasmany') {
                if (isset($this->data[$key])) {
                    //$parent = $this->getTable().'_id';
                    $this->data[$key]->setFk( $this->data[$assoc['local_key']] );
                    $this->data[$key]->save();
                }
            } elseif ($assoc['type'] == 'manytomany') {
                if (isset($this->data[$key])) {
                    $this->data[$key]->setFk( $this->data[$assoc['local_key']] );
                    $this->data[$key]->save();
                }
            } elseif ($assoc['type'] == 'hasone') {
                if (isset($this->data[$key])) {
                    $this->data[$key]->setFk( $this->data[$assoc['local_key']] );
                    $this->data[$key]->save();
                }
            }
            
            // On invalide les anciens résultats pour forcer une 
            // nouvelle requete avec ces nouveaux objets
            unset($this->data[$key]);
        }
        
        // On reset le hasChanged, vu qu'on vient d'enregistrer
        $this->hasChanged = false;
        return true;
    }

    /**
     * Delete current object from database
     * 
     * @return Void
     */
    public function delete() 
    {
        $this->delete = true;
        $this->save();
    }
    
    /**
     * Retourne le tableau de données
     * 
     * @return Array
     */
    public function getDatas()
    {
    	return $this->data;
    }
    
    /**
     * Charge l'object courant en recherchant un objet 
     * identique dans la base
     * 
     * @return Bool
     */
    public function load() 
    {
        $sqlquery = new SQLQuery();
        $items = $sqlquery->from($this->getTable());
        $columns = $this->query_columns($this->getTable());
        foreach ($columns as $c) {
            if (!empty($this->data[$c[0]])) {
                $items = $items->where($c[0]."=?", array($this->data[$c[0]]));
            }
        }
        $items->execute();
        
        if ($items->count() == 1) {
            $newitem = $items->current();
            foreach ($columns as $c) {
                $this->{$c[0]} = $newitem->{$c[0]};
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Show columns of current table
     * 
     * @return Array
     */
    public function queryColumns()
    {
    	
        $table = $this->getTable();
        
        if (empty(self::$cache[$table])) {
        
	        $s = $this->db->query("SHOW COLUMNS FROM " . $table);
	        if ($s->rowCount() <= 0) {
	            throw new LiException ('La table ' . $table . ' ne contient aucune colonne');
	        }
	        $rs = $s->fetchAll();
	        $fields = array();
	        foreach ($rs as $row) {
	            $fields[$row['Field']] = array($row['Field'], $row['Type']);
	        }
	        
	        self::$cache[$table] = $fields;
        }
        
        return self::$cache[$table];
    }
    
    /**
     * Méthode public pour savoir si un objet est une nouvelle entrée ou pas
     * 
     * @return Bool
     */
    public function isNew() 
    {
        return empty($this->data['id']);
    }
    
    /**
     * Doit-on effacer ce tuple ?
     * 
     * @return 
     */
    public function toDelete()
    {
        return $this->delete;
    }
    
    
    /*public static function getClass()
    {
    	return __CLASS__;
    }*/
    
    /**
     * Renvoi la table associé à l'objet courant
     * 
     * @return String
     */
    public function getTable() 
    {
        if (empty($this->table)) {
            return self::classToTable(get_class($this));
        }
        
        return $this->table;
    }
    
    /**
     * Méthode par défaut pour retrouver la table en fonction de la classe
     * Utilisé si la table n'est pas renseigné dans le model courant
     * 
     * @return String
     * @param String $class
     */
    public static function classToTable($class) 
    {
        return strtolower($class);
    }
    
    /**
     * Méthode par défaut pour retrouver la class en fonction de la table
     * Utilisé si la class n'est pas renseigné dans le tableau d'association
     * du model courant
     * 
     * @return String
     * @param String $table
     */
    public static function tableToClass($table) 
    {
        return ucwords($table);
    }
    
    /**
     * Getter du tableau d'association
     * 
     * @return Array
     */
    public function getAssociations() 
    {
        return $this->associations;
    }
}

