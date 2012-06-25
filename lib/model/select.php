<?php

class MfSimpleSelect
{
	protected $db;
	
	public function __construct()
	{
		$this->db = _r('db');
	}
	
	/*public function find($table, $where)
	{
		$db = self::$db;
		$db->prepare("SELECT * FROM ".)
	}*/
}