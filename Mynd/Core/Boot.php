<?php

namespace Mynd\Core;

use Mynd\Core\Url\Url;

use Mynd\Core\Registery\Registery;
use Mynd\Core\Request\Request;
use Mynd\Core\Response\Response;
use Mynd\Core\Db\Db;

/**
 * Analyse l'url puis appelle le bon couple controller/action
 */
class Boot
{
    /**
     * Le dispatcher qui va lancer l'action en fonction des paramètres
     * @return void
     */
    public static function init()
    {
        // On stock la variable d'environnement qui vient d'apache, pour cela, placer
        // SetEnv Environment (development|production|test)
        $environment = getenv('Environment');
        if ($environment === false) {
            $environment = 'production';
        }
        Registery::set('environment', $environment);

        // configuration du site
        $config = require LI_APP.'/Config/Config.php';
        Registery::set('config', $config[$environment]);
        $config = Registery::get('config');

        // configuration par default
        Registery::setAndMerge('config', array(
            'default_module' => 'Front',
            'default_controller' => 'Index',
            'default_action' => 'Index'
        ), true);

        Registery::load('routes', LI_APP.'/Config/Routes.php');

        $db_config = array();

        if (file_exists(LI_APP.'/Config/Database.php')) {

            $db_configs = require LI_APP.'/Config/Database.php';
            $db_config = $db_configs[$environment];
            Registery::set('db_config', $db_config);

            // initialisation de la bdd
            if ( ( ! empty($db_config['dsn'])) && ( ! empty($db_config['user'])) ) {
                Registery::set('db', new Db($db_config['dsn'], $db_config['user'], $db_config['pass']));
            }
        }

        // On construit l'objet requete qui va contenir les params d'entrées
        // ainsi que l'analyse de l'url pour déterminer l'app/controller/action
        // à utiliser
        $request = new Request();
        $params = &$request->getParams();
        Registery::set('params', $params);

        // On insère l'éventuel helper lié à notre application
        if (file_exists(LI_APP.'/Helper/Helper.php')) {
            require_once LI_APP.'/Helper/Helper.php';
        }

        // si le projet contient un fichier boot dans la config
        if (file_exists(LI_APP.'/Config/Boot.php')) {
            include_once LI_APP.'/Config/Boot.php';
            if (function_exists('onBoot')) {
                onBoot($request, $config);
            }
        }

        $response = self::launch($request);
        $response->out();
    }

    public static function launch(&$request)
    {
        $response = new Response();
        $params = &$request->getParams();

        // Si app/controller/action vide => 404
        if ( (empty($params['module'])) || (empty($params['controller'])) || (empty($params['action'])) ) {
            $response->body = 'URL incompl&eacute;te';
            $response->send404();
            return $response;
        }

        $moduleName = Url::toClass($params['module']);
        $controllerName = Url::toClass($params['controller']);
        $actionName = Url::toClass($params['action']);

        // Et on lance
        $controller = '\App\Modules\\'.$moduleName.'\Controller\\'.$controllerName.'Controller';
        if ( ! class_exists($controller)) {
            $response->body = 'Controleur '.$controller.' introuvable';
            $response->send404();
        }

        $oController = new $controller($request, $response);

        if (! method_exists($oController, $actionName)) {
            $response->body = 'Action '.$actionName.' introuvable';
            $response->send404();
            return $response;
        }

        $response = $oController->runAction($actionName);

        return $response;
    }
}
