<?php
class MfSimpleModel
{
	protected $db;

	protected $table;

	protected $data = array();
	
	protected $pdo;
	
	protected $cache;
	
	public static function select()
	{
		//return LiSelect::find(strtolower(__CLASS__), $where);
	}
	
	public static function one()
	{
		
	}

	//protected $cache;

	public function __construct($id = null)
	{
		$this->init();
	}
	
	public function init($id = null)
	{
		$this->db = _r('db');

		$this->table = strtolower(__CLASS__);

		if ( ! is_null($id)) {
			$this->load($id);
		}

		// @deprecated
		$this->pdo = $this->db->getPdo();

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
		$s = $this->db->prepare("SELECT * FROM ".$this->table." WHERE id = ?");
		$s->execute(array($id));

		$this->fromArray($s->fetch());
	}

	public function fromArray(array $array)
	{
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	/*public function update(array $bind)
	{
		if (empty($this->id)) {
			return false;
		}
		
		$set = array();
		$i = 0;
		foreach ($bind as $col => $val) {
			unset($bind[$col]);
			$bind[':col'.$i] = $val;
			$val = ':col'.$i;
			$i++;
			$set[] = $this->quoteIdentifier($col, true) . ' = ' . $val;
		}
		
		$bind[':id'] = $this->id;

		$sql = "UPDATE "
			. $this->quoteIdentifier($table, true)
			. ' SET ' . implode(', ', $set)
			. " WHERE id = :id";

		$stmt = $this->query($sql, $bind);
		$result = $stmt->rowCount();
		return $result;
	}

	public function insert(array $bind)
	{
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		$i = 0;
		foreach ($bind as $col => $val) {
			$cols[] = $this->quoteIdentifier($col, true);
			unset($bind[$col]);
			$bind[':col'.$i] = $val;
			$vals[] = ':col'.$i;
			$i++;
		}

		// build the statement
		$sql = "INSERT INTO "
			. $this->quoteIdentifier($table, true)
			. ' (' . implode(', ', $cols) . ') '
			. 'VALUES (' . implode(', ', $vals) . ')';

		// execute the statement and return the number of affected rows
		$stmt = $this->query($sql, $bind);
		$result = $stmt->rowCount();
		return $result;
	}

	public function delete()
	{
		if (empty($this->id)) {
			return false;
		}
		
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($table, true)
             . " WHERE id = :id";

        $stmt = $this->query($sql, array(':id' => $this->id));
        $result = $stmt->rowCount();
        return $result;
	}*/

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
}