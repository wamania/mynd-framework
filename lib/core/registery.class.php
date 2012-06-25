<?php
/**
 *
 */
class MfRegistery
{
    private static $data;

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function setAndMerge($key, $value, $before=false)
    {
        if (empty(self::$data[$key])) {
            self::$data[$key] = array();
        }
        if ($before) {
            self::$data[$key] = array_merge($value, self::$data[$key]);
        } else {
            self::$data[$key] = array_merge(self::$data[$key], $value);
        }
    }

    public static function load ($key, $file)
    {
        if (@file_exists($file)) {
            self::$data[$key] = require $file;
        } elseif (!isset(self::$data[$key])) {
            self::$data[$key] = array();
        }
    }

    public static function loadAndMerge ($key, $file, $before=false)
    {
        if (empty(self::$data[$key])) {
            self::$data[$key] = array();
        }
        if (@file_exists($file)) {
            if ($before) {
                self::$data[$key] = array_merge(require $file, self::$data[$key]);
            } else {
                self::$data[$key] = array_merge(self::$data[$key], require $file);
            }
        }
    }

    public static function get($key)
    {
        if (array_key_exists($key, self::$data)) {
            return self::$data[$key];
        }
        return null;
    }

    public static function has ($key)
    {
        return array_key_exists($key, self::$data);
    }

    public static function delete ($key)
    {
        if (array_key_exists($key, self::$data)) {
            unset(self::$data[$key]);
        }
    }
}
