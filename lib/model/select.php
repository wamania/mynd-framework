<?php

class MfSimpleSelect implements Iterator, Countable
{
    private $db;

    private $key;

    private $results;

    private $table;

    private $class;

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

    public function __construct()
    {
        $this->db = _r('db');
        $this->params = array();
        $this->results = null;
        $this->where = array();
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
    public function from($table, $class)
    {
        $this->table = $table;
        $this->class = $class;
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

    public function execute()
    {
        if ( (!empty($this->orderBy)) && (is_array($this->orderBy)) ) {
            foreach ($this->orderBy as $key => $value) {
                $this->orderBy[$key] = preg_replace ('#\+([0-9a-zA-Z\-\_]+)#', '$1 ASC', $value);
                $this->orderBy[$key] = preg_replace ('#\-([0-9a-zA-Z\-\_]+)#', '$1 DESC', $value);
            }
        }

        $query = "SELECT * FROM " . $this->table;

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

        $this->results = array();

        while ($row = $s->fetchObject($this->class)) {
            $this->results[] = $row;
        }
        $this->key = 0;
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

    public function __toString()
    {
        $order = array();

        if ( (!empty($this->orderBy)) && (is_array($this->orderBy)) ) {
            foreach ($this->orderBy as $key => $value) {
                $order[$key] = preg_replace('#\+([0-9a-zA-Z\-\_]+)#', '$1 ASC', $value);
                $order[$key] = preg_replace('#\-([0-9a-zA-Z\-\_]+)#', '$1 DESC', $value);
            }
        }

        $query = "SELECT * FROM " . $this->table;

        if (!empty($this->where)) {
            $query .= " WHERE " . implode(' AND ', $this->where);
        }

        if (! empty($this->orderBy)) {
            $query .= " ORDER BY " . implode(',', $this->orderBy);
        }

        if (! empty($this->limitLength)) {
            $query .= " LIMIT " . $this->limitOffset . "," . $this->limitLength;
        }

        return 'SQL => '.$query."\n<br>".'Params => '.print_r($this->params, true);
    }
}