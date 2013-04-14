<?php

class MfSimpleSelect implements Iterator, Countable, ArrayAccess
{
    private $db;

    private $key;

    private $results;

    private $table;

    private $primary;

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
    public function from($table, $class, $primary)
    {
        $this->table = $table;
        $this->class = $class;
        $this->primary = $primary;

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
        $this->where[] = array($where);
        if (!is_array($params)) {
            $params = array($params);
        }
        $this->params = array_merge($this->params, $params);
        return clone($this);
    }

    /**
     * Ajoute OR WHERE au dernier WHERE
     * @param String $where
     * @param mixed $params
     * @return MfSimpleSelect
     */
    public function orWhere ($where, $params=array())
    {
        // on ajoute ce orWhere au tableau du dernier where
        $this->where[ (count($this->where)-1) ][] = $where;
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
     * Synonyme of orderBy
     * @param unknown_type $str
     * @return Ambigous <object, MfSimpleSelect>
     */
    public function order($str)
    {
        $this->orderBy = func_get_args();
        return clone($this);
    }

    /**
     * Factorisation de la construction de la clause where, en prenant en compte les orWhere
     */
    public function buildWhere()
    {
        $strWhere = '';

        if (!empty($this->where)) {
            $queryWhere = array();
            foreach ($this->where as $where) {
                $queryWhere[] = implode(' OR ', $where);
            }

            $queryWhere = array_map(
                create_function('$wheres', 'return "(".$wheres.")";'),
                $queryWhere);
            $strWhere = " WHERE " . implode (' AND ', $queryWhere);
        }

        return $strWhere;
    }

    public function execute()
    {
        if ( (!empty($this->orderBy)) && (is_array($this->orderBy)) ) {
            foreach ($this->orderBy as $key => $value) {
                $this->orderBy[$key] = preg_replace ('#\+([0-9a-zA-Z\-\_]+)#', '$1 ASC', $value);
                $this->orderBy[$key] = preg_replace ('#\-([0-9a-zA-Z\-\_]+)#', '$1 DESC', $value);
            }
        }

        $query = "SELECT *  FROM " . $this->table;
        $query .= $this->buildWhere();

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
     * Renvoi un paginator utilisable par un helper
     * @param unknown_type $page
     * @param unknown_type $perPage
     * @return MfPaginatorSelect
     */
    public function paginate($page, $perPage)
    {
        // la clé primaire
        /*if (is_array($this->primary)) {
            $col = '*';
        } else {
            $col = $this->primary;
        }*/

        // on commence par le count
        $query = "SELECT COUNT(".$this->primary.") FROM ".$this->table;
        $query .= $this->buildWhere();

        $s = $this->db->prepare($query);
        $s->execute($this->params);
        $count = $s->fetchColumn();

        $this->limit($perPage, ($page-1)*$perPage);

        $paginator = new MfPaginatorSelect($page, $perPage);
        $paginator->setCount($count);
        $paginator->setSelect(clone($this));

        return $paginator;
    }

    /**
     * Synonyme de delete
     * @see delete()
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * Supprime l'ensemble des résultats
     */
    public function delete()
    {
        $query = "DELETE FROM ".$this->table;
        $query .= $this->buildWhere();

        $s = $this->db->prepare($query);
        return $s->execute($this->params);
    }

    /**
     * Mets à jours l'ensemble des résultats
     * @param array $set
     */
    public function update($datas = array())
    {
        $query = "UPDATE ".$this->table." SET ";

        $set = array();
        $params = array();
        foreach ($datas as $k => $v) {
            $set[] = $k.'=:'.$k;
            $params[':'.$k] = $v;
        }
        $query .= implode(',', $set);
        $query .= $this->buildWhere();

        $s = $this->db->prepare($query);
        return $s->execute(array_merge($params, $this->params));
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
     * Setter de l'interface ArrayAccess
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->results[$offset] = $value;
    }

    /**
     * isset de l'interface ArrayAccess
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * unset de l'interface ArrayAccess
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->results[$offset]);
    }

    /**
     * Getter de l'interface ArrayAccess
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return isset($this->results[$offset]) ? $this->results[$offset] : null;
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
     * Pour DEBUG
     * @return string
     */
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
            $queryWhere = array();
            foreach ($this->where as $where) {
                $queryWhere[] = implode(' OR ', $where);
            }
            $queryWhere = array_map(
                    create_function('$wheres', 'return "(".$wheres.")";'),
                    $queryWhere);
            $query .= " WHERE " . implode (' AND ', $queryWhere);
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