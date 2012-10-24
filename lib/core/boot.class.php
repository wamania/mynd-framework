<?php
/**
 * Analyse l'url puis appelle le bon couple controller/action
 */
class MfBoot
{
    /**
     * Le dispatcher qui va lancer l'action en fonction des paramètres
     * @return void
     */
    public static function init() {

        // On stock la variable d'environnement qui vient d'apache, pour cela, placer
        // SetEnv Environment (development|production|test)
        $environment = getenv('Environment');
        if ($environment === false) {
            $environment = 'production';
        }
        MfRegistery::set('environment', $environment);

        // configuration du site
        MfRegistery::load('config', LI_APP.'config/config.php');

        // configuration par default
        MfRegistery::setAndMerge('config', array(
            'default_module' => 'default',
            'default_controller' => 'default',
            'default_action' => 'index'
        ), true);

        MfRegistery::load('routes', LI_APP.'config/routes.php');

        $db_config = array();

        if (file_exists(LI_APP.'config/database.php')) {

            MfRegistery::load('db_config', LI_APP.'config/database.php');

            // initialisation du model
            $db_config = _r('db_config');

            if ( ( ! empty($db_config['dsn'])) && ( ! empty($db_config['user'])) ) {
                MfRegistery::set('db', new MfDb($db_config['dsn'], $db_config['user'], $db_config['pass']));

                $model = _c('model');
                if ( (empty($model)) || ($model == 'simple') ) {
                    spl_autoload_register(array('MfInitSimpleModel', 'includeModel'));

                } elseif ($model == 'orm') {
                    spl_autoload_register(array('MfInitOrm', 'includeModel'));
                }
            }
        }

        // On construit l'objet requete qui va contenir les params d'entrées
        // ainsi que l'analyse de l'url pour déterminer l'app/controller/action
        // à utiliser
        $request = new MfRequest();
        $params = &$request->getParams();
        MfRegistery::set('params', $params);
        $config = MfRegistery::get('config');

        // On insère l'éventuel helper lié à notre application
        if (file_exists(LI_APP.'helper/helper.php')) {
            require_once LI_APP.'helper/helper.php';
        }

        // si le projet contient un fichier boot dans la config
        if (file_exists(LI_APP.'config/boot.php')) {
            include_once LI_APP.'config/boot.php';
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
        $response = new MfResponse();
        $params = &$request->getParams();

        // Si app/controller/action vide => 404
        if ( (empty($params['module'])) || (empty($params['controller'])) || (empty($params['action'])) ) {
            $response->body = 'URL incompl&eacute;te';
            $response->send404();
            return $response;
        }

        $controllerPath = LI_APP.'modules/'.$params['module'].'/controller/'.$params['controller'].'Controller.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
        } else {
            $response->body = 'Controleur '.$params['controller'].' introuvable ('.$controllerPath.' )';
            $response->send404();
            return $response;
        }

        // Et on lance
        $controller = $params['controller'].'Controller';
        if ( ! class_exists($controller)) {
            $response->body = 'Controleur '.$controller.' introuvable';
            $response->send404();
        }

        $oController = new $controller($request, $response);

        if (! method_exists($oController, $params['action'])) {
            $response->body = 'Action '.$params['action'].' introuvable';
            $response->send404();
            return $response;
        }

        $response = $oController->runAction($params['action']);

        $next = $oController->getNext();
        if ( ! is_null($next)) {
            return $next;
        }

        return $response;
    }
}
