<?php
class MfSimpleModel
{
    protected $db;

    public static $table;

    private static $cache;

    protected $data = array();

    public static function select()
    {
        $class = get_called_class();
        $select = new MfSimpleSelect();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }

        $select =  $select->from(static::$table, $class);
        if (!empty($where)) {
            $select = $select->where($where, $params);
        }
        return $select;
    }

    public static function one($where, $params=null)
    {
        $class = get_called_class();
        $select = new MfSimpleSelect();

        if (empty(static::$table)) {
            static::$table = strtolower($class);
        }

        $select =  $select->from(static::$table, $class);
        if (is_numeric($where)) {
            $select = $select->where("id=".$where);
        } else {
            if (!is_array($params)) {
                $params = array($params);
            }
            $select = $select->where($where, $params);
        }
        $current = $select->current();
        if (empty($current)) {
            return null;
        }
        return $current;
    }

    public function __construct($id = null)
    {
        $this->init();
    }

    public function init($id = null)
    {
        $this->db = _r('db');

        if (empty(static::$table)) {
            static::$table = strtolower(__CLASS__);
        }

        if ( ! is_null($id)) {
            $this->load($id);
        }

        // cache
        $cache = _c('cache');
        if (empty($cache)) {
            $cache = 'MfFakecache';
        } else {
            $cache = 'Mf'.ucwords($cache);
        }

        $this->cache = new $cache;

        $cacheOptions = _c('cache_options');
        if (!empty($cacheOptions)) {
            $this->cache->setOptions($cacheOptions);
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

    public function load($id)
    {
        $s = $this->db->prepare("SELECT * FROM ".static::$table." WHERE id = ?");
        $s->execute(array($id));

        $this->inject($s->fetch());
    }

    public function inject(array $array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    public function save(array $array)
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
                if (isset($this->{$c})) {
                    $cols[] = $c;
                    $vals[] = ':'.$c;
                    $params[':'.$c] = $this->{$c};
                }
            }
            $sql = "INSERT INTO " . static::$table . ' (' . implode(', ', $cols) . ') ' . 'VALUES (' . implode(', ', $vals) . ')';
            $s = $this->db->prepare($sql);
            return $s->execute($params);

            // update
        } else {
            $set = array();
            $params = array();

            foreach ($columns as $c) {
                if (isset($this->{$c})) {
                    $set[] = $c.'=:'.$c;
                    $params[':'.$c] = $this->{$c};
                }
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
        if (empty(self::$cache[$this->table])) {

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