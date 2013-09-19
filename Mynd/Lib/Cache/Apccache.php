<?php

namespace Mynd\Lib\Cache;

class Apccache
{
    public function __construct()
    {

    }

    public function setOptions($options)
    {

    }

    public function get($key)
    {
        $isSuccess = false;
        $value = apc_fetch($key, $isSuccess);

        if ($isSuccess) {
            return unserialize($value);
        }

        return null;
    }

    public function set($key, $value, $ttl=0)
    {
        if (!empty($key)) {
            return apc_store($key, serialize($value), $ttl);
        }

        return false;
    }

    /*public function __isset($key)
     {
    $isSuccess = false;
    $value = apc_fetch($key, $isSuccess);

    return $isSuccess;
    }

    public function __unset($key)
    {
    if (!empty($key)) {
    return apc_delete($key);
    }

    return false;
    }*/
}