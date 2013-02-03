<?php

/**
 * Classe model, représente une instance d'un modèle
 * @author wamania
 *
 */
class MfSimpleModel
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
        $select = new MfSimpleSelect();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }

        $select =  $select->from(static::$table, $class, static::$primary);
        if (!is_null($where)) {
            if (!is_array($params)) {
                $params = array($params);
            }
            $select = $select->where($where, $params);
        }
        return $select;
    }

    /**
     * Portail vers le selecteur pour sortir 1 ligne seulement
     *
     * @param mixed $where
     * @param array $params
     * @return object of MfSimpleModel
     */
    public static function one($where, $params=null)
    {
        $class = get_called_class();
        $select = new MfSimpleSelect();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }

        $select =  $select->from(static::$table, $class, static::$primary);

        // 1er cas, clé composée
        if (is_array($where)) {
            if (!is_array(static::$primary)) {
                throw new MfModelException('Ce model a une primary key simple !', 101);
                return;
            }
            if (count($where) != count(static::$primary)) {
                throw new MfModelException('Ce model a une clé primaire composé de '.count(static::$primary).' champs. '.count($where).' donnés !', 103);
                return;
            }
            foreach (static::$primary as $k => $primary) {
                $select = $select->where($primary.'=?', $where[$k]);
            }

        // clé numérique
        } elseif (is_numeric($where)) {
            $select = $select->where(static::$primary."=?", $where);

        } elseif (is_string($where)) {
            if (!is_array($params)) {
                $params = array($params);
            }
            $select = $select->where($where, $params);

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
     * Constructor
     * @param unknown_type $params
     */
    public function __construct($id = null)
    {
        /*$this->init($params);*/
        $this->init();

        if (! is_null($id)) {
            $this->load($id);
        }
    }

    /**
     * Init, placé ici et pas dans le constructor pour l'utiliser dans __wakeup
     * @param array $params
     */
    public function init(/*$params = null*/)
    {
        $this->db = _r('db');

        if (empty(static::$table)) {
            static::$table = strtolower(get_called_class());
        }

        /*if ( ! is_null($params)) {
            if (is_numeric($params)) {
                $this->load($params);

            } elseif (is_array($params)) {
                $this->inject($params);
            }
        }*/
    }

    public function __sleep()
    {
        return array('data');
    }

    public function __wakeup()
    {
        $this->init();
    }

    /**
     * Charge l'objet courant désigné par sa clé primaire (simple ou composé)
     *
     * @param Int|Mixed $id
     */
    public function load($id)
    {

        /*$where = array();

        if (is_array($id)) {
            if (!is_array(static::$primary)) {
                throw new MfModelException('Ce model a une primary key simple !', 101);
                return;
            }
            if (count($id) != count(static::$primary)) {
                throw new MfModelException('Ce model a une clé primaire composé de '.count(static::$primary).' champs. '.count($id).' donnés !', 103);
                return;
            }
            foreach (static::$primary as $k => $primary) {
                $select = $select->where($primary.'=?', $where[$k]);
            }
        } else {

        }

        $s = $this->db->prepare("SELECT * FROM ".static::$table." WHERE id = ?");
        $s->execute(array($id));
        $row = $s->fetch();

        if (is_array($row)) {
            $this->inject($row);
        }*/
    }

    public function inject(array $array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

    public function save(array $array = array())
    {
        if (!empty($array)) {
            $this->inject($array);
        }
        $columns = $this->queryColumns();

        // insert
        if (empty($this->id)) {
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

            $this->id = $this->db->lastInsertId();

            return $check;

            // update
        } else {
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
            $sql = "UPDATE " . static::$table . ' SET ' . implode(', ', $set) . " WHERE id = :id";
            $s = $this->db->prepare($sql);
            return $s->execute($params);
        }
    }

    public function delete()
    {
        if (!empty($this->id)) {
            $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
            $s = $this->db->prepare($sql);
            return $s->execute(array($this->id));
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