<?php
/**
 * Classe de session simple
 */
class MfPHPSession implements ArrayAccess 
{
	public function start()
	{
		session_start();
	}

	public function stop()
	{
		session_write_close();
		$_SESSION = array();
		@session_unset();
		@session_destroy();
	}

	public function getSessionId() 
	{
		return session_id();
	}

	public function offsetExists($offset)
	{
		return isset($_SESSION[$offset]);
	}

	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) return $_SESSION[$offset];
		return null;
	}

	public function offsetSet($offset, $value)
	{
		$_SESSION[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset)) unset($_SESSION[$offset]);
	}

	public function __get($offset) 
	{
		if ($this->offsetExists($offset)) return $_SESSION[$offset];
		return null;
	}

	public function __set($offset, $value) 
	{
		$_SESSION[$offset] = $value;
	}

	public function __isset($offset) 
	{
		return isset($_SESSION[$offset]);
	}

	public function __unset($offset) 
	{
		if ($this->offsetExists($offset)) unset($_SESSION[$offset]);
	}
}
?>
