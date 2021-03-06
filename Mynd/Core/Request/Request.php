<?php

namespace Mynd\Core\Request;

use \Mynd\Core\Registery\Registery;

class Request
{
    private $params;

    private $routeName;

    public function __construct()
    {

    }

    private function stripslashes()
    {
        if (get_magic_quotes_gpc()) {
            function stripslashes_deep($value)
            {
                $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
                return $value;
            }

            $_POST = array_map('stripslashes_deep', $_POST);
            $_GET = array_map('stripslashes_deep', $_GET);
            $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
            $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
        }
    }

    public function init()
    {
        // On commence à virer les magic quote (beuuuuuuuuuuuurk)
        $this->stripslashes();

        $config = Registery::get('config');

        if ($config['url_handler'] == 'modrewrite') {
            $pathinfo = (isset($_GET['pathinfo']) ? $_GET['pathinfo'] : '');

        } elseif ($config['url_handler'] == 'multiviews') {
            if (isset($_SERVER['PATH_INFO'])) {
                $pathinfo =  substr($_SERVER['PATH_INFO'], 1, strlen($_SERVER['PATH_INFO'])-1);
            }

        } elseif ($config['url_handler'] == 'querystring') {
            if (isset($_GET['qs'])) {
                $pathinfo =  substr($_GET['qs'], 1, strlen($_GET['qs'])-1);
            }

        } elseif ($config['url_handler'] == 'simple') {
            // Do nothing !
        } else {
            throw new \Exception ('Impossible de trouver un support pour ce type d\'url');
        }

        $urlEngineClassName = 'Mynd\Core\Url\\'.ucwords($config['url_handler']).'Url';
        try {
            $urlEngine = new $urlEngineClassName;
            $route = $urlEngine->url2params($pathinfo, $_GET);

            $this->params = $route['params'];
            $this->routeName = $route['route_name'];

            if (isset($this->params['pathinfo'])) {
                unset($this->params['pathinfo']);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }

        $this->params = array_merge($this->params, $_POST);

        // On stocke les paramètres dans le registre
        Registery::set('route_name', $this->routeName);
        Registery::set('params', $this->params);
    }

    public function &getParams()
    {
        if (!is_array($this->params)) {
            $this->init();
        }
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     *
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }


    public static function is_post()
    {
        return (self::method() == 'POST');
    }

    public static function protocol()
    {
        if ( (isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on') ) {
            return 'https://';
        }
        return 'http://';
    }
}
