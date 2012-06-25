<?php

class MfMemcache
{
    private $memcache;

    public function __construct()
    {
        $this->memcache = new Memcache;
    }

    public function setOptions($options)
    {
        if (!empty($options['servers'])) {
            foreach ($options['servers'] as $server) {
                $this->memcache->connect($server[0], $server[1]);
            }
        }
    }

    public function get($key)
    {
        $value = $this->memcache->get($key);

        if ($value !== false) {
            return $value;
        }

        return null;
    }

    public function set($key, $value, $ttl=7200)
    {
        $this->memcache->set($key, $value, false, $ttl);

        return true;
    }

    public function delete($key)
    {
        $this->memcache->delete($key);

        return true;
    }
}