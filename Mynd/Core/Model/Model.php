<?php

namespace Mynd\Core\Model;

/**
 * Classe model, représente une instance d'un modèle
 * @author wamania
 *
 */
class Model
{
    /**
     * L'object db (decorator de PDO)
     * @var unknown_type
     */
    protected $db;

    /**
     * Nom de la table, optionel si la table est strtolower(__CLASS__)
     * @var unknown_type
     */
    public static $table;

    /**
     * Clé primaire
     * @var String|Array
     */
    public static $primary = 'id';

    /**
     * un cache très simple pour stocker les colonnes
     * @var Array
     */
    private static $cache;

    /**
     * Les données de l'instance du model
     * @var unknown_type
     */
    protected $data = array();

    /**
     * Portail vers le selecteur
     *
     * @return Array
     */
    public static function select($where=null, $params=null)
    {
        $class = get_called_class();
        $select = new \Mynd\Core\Model\Select();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }
        $select->from(static::$table, $class, static::$primary);

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }

        if (!is_null($where)) {
            if (!is_array($params)) {
                $params = array($params);
            }
            $select->where($where, $params);
        }
        return $select;
    }

    /**
     * Portail vers le selecteur pour sortir 1 ligne seulement
     *
     * @param mixed $where (primary key, array of primary keys or string)
     * @param array $params
     * @return object of MfSimpleModel
     */
    public static function one($where, $params=null)
    {
        $class = get_called_class();
        $select = new \Mynd\Core\Model\Select();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }
        $select->from(static::$table, $class, static::$primary);

        // clé primaire
        if (is_numeric($where)) {
            $select->where(static::$primary."=?", $where);

        } elseif (is_string($where)) {

            if (!is_array($params)) {
                $params = array($params);
            }
            $select->where($where, $params);

        } else {
            throw new MfModelException('Le 1er paramètre de la méthode static MfSimpleModel::one() est de type inconnu !', 102);
            return;
        }
        $current = $select->current();
        if (empty($current)) {
            return null;
        }
        return $current;
    }

    /**
     * Une methode fetch qui retourne un tableau d'objets de la classe courante
     * @param PDOStatement $s
     */
    public static function fetch(\PDOStatement $s)
    {
        $class = get_called_class();

        $lines = array();
        while ($line = $s->fetchObject($class)) {
            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * Constructor
     * @param unknown_type $params
     */
    public function __construct(array $data = array())
    {
        $this->init();

        if (! empty($data)) {
            $this->inject($data);
        }
    }

    /**
     * Init, placé ici et pas dans le constructor pour l'utiliser dans __wakeup
     * @param array $params
     */
    public function init()
    {
        $this->db = _r('db');

        if (empty(static::$table)) {
            static::$table = strtolower(get_called_class());
        }
    }

    public function __sleep()
    {
        return array('data');
    }

    public function __wakeup()
    {
        $this->init();
    }

    public function inject(array $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

    public function save(array $array = array())
    {
        if (!empty($array)) {
            $this->inject($array);
        }

        // insert
        if (empty($this->{static::$primary})) {
            return $this->insert();

        // update
        } else {
            return $this->update();
        }

        return false;
    }

    public function insert(array $array = array())
    {
        /*if (!empty($array)) {
            $this->inject($array);
        }*/
        $columns = $this->queryColumns();

        // insert
        //if (empty($this->id)) {
            $cols = array();
            $vals = array();
            $params = array();

            foreach ($columns as $c) {
                if (isset($this->data[$c])) {
                    $cols[] = $c;
                    $vals[] = ':'.$c;
                    $params[':'.$c] = $this->data[$c];
                }
            }
            if ( (in_array('created_on', $columns)) && (!in_array('created_on', $cols)) ) {
                $cols[] = 'created_on';
                $vals[] = ':created_on';
                $params[':created_on'] = date('Y-m-d H:i:s');
            }

            $sql = "INSERT INTO " . static::$table . ' (' . implode(', ', $cols) . ') ' . 'VALUES (' . implode(', ', $vals) . ')';
            $s = $this->db->prepare($sql);
            $check = $s->execute($params);

            $this->{static::$primary} = $this->db->lastInsertId();

            return $check;
        //}
    }

    public function update(array $array = array())
    {
        /*if (!empty($array)) {
            $this->inject($array);
        }*/
        $columns = $this->queryColumns();

        // insert
        //if ( ! empty($this->id)) {
            $set = array();
            $params = array();

            foreach ($columns as $c) {
                if (isset($this->data[$c])) {
                    $set[] = $c.'=:'.$c;
                    $params[':'.$c] = $this->data[$c];
                }
            }
            if (in_array('updated_on', $columns)) {
                $set[] = 'updated_on=:updated_on';
                $params[':updated_on'] = date('Y-m-d H:i:s');
            }
            $sql = "UPDATE " . static::$table . ' SET ' . implode(', ', $set) . " WHERE ".static::$primary." = :".static::$primary;

            $s = $this->db->prepare($sql);
            return $s->execute($params);
        //}

        //return false;
    }

    public function delete()
    {
        if (!empty($this->{static::$primary})) {
            $sql = "DELETE FROM " . static::$table . " WHERE ".static::$primary." = ?";
            $s = $this->db->prepare($sql);
            return $s->execute(array($this->{static::$primary}));
        }

        return false;
    }

    public function remove()
    {
        return $this->delete();
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function getDatas()
    {
        return $this->data;
    }

    /**
     * Show columns of current table
     *
     * @return Array
     */
    public function queryColumns()
    {
        if (empty(self::$cache[static::$table])) {

            $s = $this->db->query("SHOW COLUMNS FROM " . static::$table);
            if ($s->rowCount() <= 0) {
                throw new LiException ('La table ' . $table . ' ne contient aucune colonne');
            }
            $rs = $s->fetchAll();
            $fields = array();
            foreach ($rs as $row) {
                $fields[] = $row['Field'];
            }

            self::$cache[static::$table] = $fields;
        }

        return self::$cache[static::$table];
    }
}