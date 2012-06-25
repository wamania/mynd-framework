<?php
/**
 * Fichier sqlquery.php
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
 * Classe de génération de requête sql SELECT
 * 
 * Cette classe est utilisée par la class Model et par les associations.
 * Elle permet de remplir les model avec les données de la base.
 * Elle ne doit pas être utilisée directement
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
class MfSqlQuery implements Iterator, Countable 
{
    /**
     * @var String
     */
    private $from;
    
    /**
     * @var Array
     */
    private $join;
    
    /**
     * Liste des jointures déjà rélisées
     * Pour éviter les doublons
     * @var Array
     */
    private $joinKeys;
    
    /**
     * @var String
     */
    private $where;
    
    /**
     * @var Integer
     */
    private $limitLength;
    
    /**
     * @var Integer
     */
    private $limitOffset;
    
    /**
     * @var String
     */
    private $orderBy;
    
    /**
     * @var Array
     */
    private $params;
    
    /**
     * @var Array
     */
    protected $results;
    
    /**
     * @var Integer
     */
    private $key;
    
    /**
     * @var Object of LiDb
     * @see LiDb
     */
    protected $db;
    
    /**
     * Utilisé pour faciliter les jointures.
     * Rempli directement avec les informations du Model correspondant
     * 
     * @var Array
     */
    private $associations;
    
    /**
     * Constructeur par défaut
     *
     * @return void
     */
    public function __construct() 
    {
        $this->db = _r('db');
        $this->params = array();
        $this->results = null;
        $this->where = array();
        $this->join = array();
        $this->joinKeys = array();
        $this->associations = null;
    }
    
    /**
     * Surcharge de la fonction magique clone
     * de façon à pouvoir chaîner les méthodes
     * <code>
     * $query->from(..)->where(...)->limit(..)->orderby(...);
     * </code>
     * 
     * @return void
     */
    public function __clone() 
    {
        $this->key = 0;
        $this->results = null;
    }
    
    /**
     * Getter de l'attribut from
     * 
     * @return Object of LiSqlQuery Retourne un clone de lui-même
     * @param object $class
     */
    public function from ($class) 
    {
        $this->from = $class;
        return clone($this);
    }
    
    /**
     * Getter de l'objet join.
     * Permet d'ajouter des jointures dans la requete
     * 
     * @return Object of LiSqlQuery Retourne un clone de lui-même
     * @param String $key
     */
    public function join($key) 
    {
        if (is_null($this->associations)) {
            $this->setAssociations();
        }
        
        if ( (isset($this->associations[$key])) && (!in_array($key, $this->joinKeys)) ) {
            $this->join[] = array_merge($this->associations[$key], array('key'=>$key));
            $this->joinKeys[] = $key;
        }

        return clone($this);
    }
    
    /**
     * Getter de l'attribut where
     * 
     * @return Object of LiSqlQuery Retourne un clone de lui-même
     * @param object $where
     * @param array $params[optional]
     */
    public function where ($where, $params=array()) 
    {
        // Gestion des jointures dans le where
        if (preg_match_all('#([0-9a-zA-Z_]+)\.#', $where, $results)) {
            if (is_null($this->associations)) {
                $this->setAssociations();
            }
            
            foreach ($results[1] as $index => $key) {
                if (isset($this->associations[$key])) {
                    $this->join($key);
                    $table = $this->classToTable($this->associations[$key]['class']);
                    $where = str_replace($results[0][$index], $table.'.', $where);
                }
            }
            
        }
        
        $this->where[] = $where;
        if (!is_array($params)) {
            $params = array($params);
        }
        $this->params = array_merge($this->params, $params);
        return clone($this);
    }
    
    /**
     Getter de l'attribut limit et limitOffset
     
     * @return Object of LiSqlQuery Retourne un clone de lui-même
     * @param Integer $limit
     * @param Integer $offset[optional]
     */
    public function limit($length, $offset=0) 
    {
        $this->limitLength = $length;
        $this->limitOffset = $offset;
        return clone($this);
    }
    
    /**
     * Getter de l'attribut orderBy
     * 
     * @return Object of LiSqlQuery Retourne un clone de lui-même
     * @param String $str Utilise les signes + et - au lieu de ASC et DESC: '-name'
     */
    public function orderBy($str) 
    {
        $this->orderBy = func_get_args();
        return clone($this);
    }
    
    /**
     * Méthode d'exécution de la reqûete SELECT
     * Rempli l'attribut $this->results
     * 
     * @return void
     */
    public function execute() 
    {
        if ( (!empty($this->orderBy)) && (is_array($this->orderBy)) ) {
            foreach ($this->orderBy as $key => $value) {
                $this->orderBy[$key] = preg_replace ('#\+([0-9a-zA-Z\-\_]+)#', '$1 ASC', $value);
                $this->orderBy[$key] = preg_replace ('#\-([0-9a-zA-Z\-\_]+)#', '$1 DESC', $value);
            }
        }
        
        // On récupére les noms des tables liées aux classes dans les jointures
        foreach ($this->join as $key => $join) {
            $this->join[$key]['table'] = $this->classToTable($join['class']);
        }
        
        $query = "SELECT ";
        
        $query_select = array();

        $query_select = $this->encodeAlias($this->from);

        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $query_select = array_merge($this->encodeAlias($join['class']), $query_select);
            }
        }
        
        $query .= implode(',', $query_select);
        $query .= " FROM " . $this->classToTable($this->from);

        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                if ($join['type'] == 'manytomany') {
                    if ($this->classToTable($this->from) > $join['table']) {
                        $jointable = $join['table'].'_'.$this->classToTable($this->from);
                    } else {
                        $jointable = $this->classToTable($this->from).'_'.$join['table'];
                    }
                    $query .= " 
                        LEFT OUTER JOIN " . $jointable . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . "=" . $jointable . "." . $this->classToTable($this->from) . "_" . $join['local_key'] . ")
                        LEFT OUTER JOIN " . $join['table'] . " ON (" . $jointable . "." . $join['table'] . "_" . $join['foreign_key'] . "=" . $join['table'] . "." . $join['foreign_key'] . ") ";
                
                } elseif ( ($join['type'] == 'hasmany') || ($join['type'] == 'hasone') ) {
                    $query .= " LEFT OUTER JOIN " . $join['table'] . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . "=" . $join['table'] . '.' . $join['foreign_key'] . ') ';
                
                } elseif ($join['type'] == 'belongsto') {
                    $query .= " INNER JOIN " . $join['table'] . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . '=' . $join['table'] . '.' . $join['foreign_key'].') ';
                }
            }
        }
        if (!empty($this->where)) {
            $query .= " WHERE " . implode(' AND ', $this->where);
        }
        
        if (! empty($this->orderBy)) {
            $query .= " ORDER BY " . implode(',', $this->orderBy);
        }
        
        if (! empty($this->limitLength)) {
            $query .= " LIMIT " . $this->limitOffset . "," . $this->limitLength;
        }

        $s = $this->db->prepare($query);
        $s->execute($this->params);
        
        // On cherche la class model correspondante à la table
        if (!class_exists($this->from)) {
            throw new Exception ('Model '.$class.' introuvable');
        }
        $results = $s->fetchAll();

        $this->results = array();
        $records = array();
        $model = null;
        
        foreach ($results as $row) {
            
            // la table FROM
            $from_id = $row[$this->classToTable($this->from).'_id'];

            // Nouvelle entrée
            if ( !isset($records[$from_id])) {
                $from = $this->decodeAlias($this->from, $row);

                $records[$from_id] = new $this->from ($this->decodeAlias($this->from, $row));
            }

            if (!empty($this->join)) {
                foreach ($this->join as $join) {
                    if (!empty($row[$join['table'].'_id'])) {
                        $classname = $join['class'];
                        $assoc_model = new $classname($this->decodeAlias($join['class'], $row));
                        
                        $records[$from_id]->{$join['key']}->push($assoc_model);
                    }
                }
            }
        }

        $this->results = array_values($records);
        $this->key = 0;
    }
    
    /**
     * Méthode d'encodage du nom des columns dans les requêtes
     * pour éviter les conflits de noms entre plusieurs tables
     * Pour cela, on ajoute le nom de la table dans le nom de l'alias
     * 
     * @see decodeAlias
     * @return Array Les noms de columns préfixés du nom de la table
     * @param Array $class
     */
    private function encodeAlias($class) 
    {
        $aliases = array();
        $table = $this->classToTable($class);
        $model = new $class;
        $columns = $model->queryColumns();
        foreach ($columns as $column) {
            $aliases[] = $table.'.'.$column[0].' AS '.$table.'_'.$column[0];
        }
        
        return $aliases;
    }
    
    /**
     * Inverse d'encodeAlias
     * Récupère un tableau indexé par le vrai noms des columns
     * de la table au lieu des alias
     * 
     * @see encodeAlias
     * @return Array
     * @param String $class
     * @param Array $row
     */
    private function decodeAlias($class, $row) 
    {
        $attributs = array();
        $table = $this->classToTable($class);
        $model = new $class;
        $columns = $model->queryColumns();
        foreach ($columns as $column) {
            $index = $table.'_'.$column[0];
            $attributs[$column[0]] = $row[$index];
        }

        return $attributs;
    }
    
    public function paginate($length, $page)
    {
        // Get count
        $query = "SELECT COUNT(f.id) as nb";
        $query .= " FROM " . $this->classToTable($this->from). " f";

        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                if ($join['type'] == 'manytomany') {
                    if ($this->classToTable($this->from) > $join['table']) {
                        $jointable = $join['table'].'_'.$this->classToTable($this->from);
                    } else {
                        $jointable = $this->classToTable($this->from).'_'.$join['table'];
                    }
                    $query .= " 
                        LEFT OUTER JOIN " . $jointable . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . "=" . $jointable . "." . $this->classToTable($this->from) . "_" . $join['local_key'] . ")
                        LEFT OUTER JOIN " . $join['table'] . " ON (" . $jointable . "." . $join['table'] . "_" . $join['foreign_key'] . "=" . $join['table'] . "." . $join['foreign_key'] . ") ";
                
                } elseif ( ($join['type'] == 'hasmany') || ($join['type'] == 'hasone') ) {
                    $query .= " LEFT OUTER JOIN " . $join['table'] . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . "=" . $join['table'] . '.' . $join['foreign_key'] . ') ';
                
                } elseif ($join['type'] == 'belongsto') {
                    $query .= " INNER JOIN " . $join['table'] . " ON (" . $this->classToTable($this->from) . "." . $join['local_key'] . '=' . $join['table'] . '.' . $join['foreign_key'].') ';
                }
            }
        }
        if (!empty($this->where)) {
            $query .= " WHERE " . implode(' AND ', $this->where);
        }

        $s = $this->db->prepare($query);
        $s->execute($this->params);
        $resultat = $s->fetchAll();
        $count = $resultat[0]['nb'];

        return array('count' => $count, 'page_count'=>ceil($count/$length), 'current' => $page, 'objects' => $this->limit($length, (($page-1)*$length)));
    }
    
    /**
     * Implémentation de la méthode rewind de l'interface Iterator
     
     * @return void
     */
    public function rewind() 
    {
        $this->key = 0;
    }

    /**
     * Implémentation de la méthode current de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     * 
     * @return Object of LiModel L'objet courant dans l'itérateur
     */
    public function current() 
    {
        if ($this->results === null) {
            $this->execute();
        }
        if (!isset($this->results[$this->key])) {
            return null;
        }
        return $this->results[$this->key];
    }

    /**
     * Implémentation de la méthode key de l'interface Iterator
     * 
     * @return 
     */
    public function key() 
    {
        return $this->key;
    }

    /**
     * Implémentation de la méthode next de l'interface Iterator
     * 
     * @return void
     */
    public function next() 
    {
        $this->key++;
    }
    
    /**
     * Implémentation de la méthode valid de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     * 
     * @return void
     */
    public function valid() 
    {
        if ($this->results === null) {
            $this->execute();
        }
        return isset($this->results[$this->key]);
    }
    
    /**
     * Renvoie le tableau de résultat complet
     * @return multitype:
     */
    public function fetchAll()
    {
    	if ($this->results === null) {
    		$this->execute();
    	}
    	return $this->results;
    }
    
    /**
     * Implémentation de la méthode count de l'interface Coutable
     * 
     * @return Integer
     */
    public function count() 
    {
        if ($this->results === null) {
            $this->execute();
        }
        return count($this->results);
    }
    
    /**
     * Récupère le nom de la table associée à une class Model
     * 
     * @return String
     * @param String $class
     */
    private function classToTable($class) 
    {
        $oModel = new $class;
        $table = $oModel->getTable();
        
        if (!empty($table)) {
            return $table;
        }

        return LiModel::classToTable($class);
    }
    
    /**
     * Rempli le tableau $associations en utilisant le model courant
     * 
     * @return void
     */
    public function setAssociations() 
    {
        if (empty($this->from)) {
            return false;
        }
        
        $model = new $this->from;
        $this->associations = $model->getAssociations();
    }
}

