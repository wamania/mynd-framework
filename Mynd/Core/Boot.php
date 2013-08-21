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
            'default_module' => 'Index',
            'default_controller' => 'Index',
            'default_action' => 'Index'
        ), true);

        Registery::load('routes', LI_APP.'/Config/Routes.php');

        $db_config = array();

        if (file_exists(LI_APP.'/Config/Database.php')) {

            $db_configs = require LI_APP.'/Config/Database.php';
            $db_config = $db_configs[$environment];
            Registery::set('db_config', $db_config);

            // initialisation du model
            //$db_config = _r('db_config');

            if ( ( ! empty($db_config['dsn'])) && ( ! empty($db_config['user'])) ) {
                Registery::set('db', new Db($db_config['dsn'], $db_config['user'], $db_config['pass']));

                /*$model = _c('model');
                if ( (empty($model)) || ($model == 'simple') ) {
                    spl_autoload_register(array('MfInitSimpleModel', 'includeModel'));

                } elseif ($model == 'orm') {
                    spl_autoload_register(array('MfInitOrm', 'includeModel'));
                }*/
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

        // la reponse est une requete, on la lance
        /*if ($response instanceof MfRequest) {
            $response = self::launch($response);
        }*/

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

        /*$controllerPath = LI_APP.'Modules/'.$params['module'].'/Controller/'.$params['controller'].'Controller.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
        } else {
            $response->body = 'Controleur '.$params['controller'].' introuvable ('.$controllerPath.' )';
            $response->send404();
            return $response;
        }*/

        // détermination du nom du module
        /*$tabModule = explode('-', $params['module']);
        $moduleName = '';
        foreach ($tabModule as $partModule) {
            $moduleName .= ucfirst($partModule);
        }

        // détermination du nom du controlleur
        $tabController = explode('-', $params['controller']);
        $controllerName = '';
        foreach ($tabController as $partController) {
            $controllerName .= ucfirst($partController);
        }

        // détermination du nom de l'action
        $tabAction = explode('-', $params['action']);
        $actionName = '';
        foreach ($tabAction as $partAction) {
            $actionName .= ucfirst($partAction);
        }*/
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

        /*$next = $oController->getNext();
        if ( ! is_null($next)) {
            return $next;
        }*/

        return $response;
    }
}
