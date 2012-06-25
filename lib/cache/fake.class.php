<?php

class LiFakecache
{
	public function __construct()
	{
		
	}
	
	public function setOptions($options)
	{

	}
	
	public function set($key, $value, $ttl=0)
	{
		return true;
	}
	
	public function get($key)
	{
		return null;
	}
	
	/*public function __isset($key)
	{
		return false;
	}
	
	public function __unset($key)
	{
		return true;
	}*/
}