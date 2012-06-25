<?php
/**
 * Analyse l'url puis appelle le bon couple controller/action
 */
class LiBoot 
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
		LiRegistery::set('environment', $environment);

		// configuration du site
		LiRegistery::load('config', LI_APP.'config/config.php');
		
		// configuration par default
		LiRegistery::setAndMerge('config', array(
			'default_module' => 'default',
			'default_controller' => 'default',
			'default_action' => 'index'
		));
		
		
		LiRegistery::load('routes', LI_APP.'config/routes.php');

		$db_config = array();

		if (file_exists(LI_APP.'config/database.php')) {
			 
			LiRegistery::load('db_config', LI_APP.'config/database.php');
			
			// initialisation du model
			$db_config = _r('db_config');
			
			if ( ( ! empty($db_config['dsn'])) && ( ! empty($db_config['user'])) && ( ! empty($db_config['pass'])) ) {
				LiRegistery::set('db', new LiDb($db_config['dsn'], $db_config['user'], $db_config['pass']));
				//LiSelect::setDb(_r('db'));
				
				if (_c('model') == 'orm') {
					spl_autoload_register(array('LiInitOrm', 'includeModel'));
				
				} elseif (_c('model') == 'simple') {
					spl_autoload_register(array('LiInitSimpleModel', 'includeModel'));
				}
			}
			
			/*if (class_exists('LiInitOrm')) {
				LiInitOrm::init(LI_APP.'/model/', _r('db'));
			}*/
		}

		// On construit l'objet requete qui va contenir les params d'entrées
		// ainsi que l'analyse de l'url pour déterminer l'app/controller/action
		// à utiliser
		$request = new LiRequest();
		$params = &$request->getParams();
		$config = LiRegistery::get('config');

		// initialisation du model
		//$db_config = _r('db_config');

		/*if (!array_key_exists('dsn', $db_config)) {
			foreach ($db_config as $key => $value) {
				LiRegistery::set('db_'.$key, new LiDb($value));
			}
			 
		} else {*/
			//LiInitModel::init(LI_APP.'/model', _r('db'));
			//LiRegistery::set('db', new LiDb($db_config));
		//}

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
		if ($response instanceof LiRequest) {
			$response = self::launch($response);
		}

		$response->out();
	}
	
	public static function launch(&$request)
	{
		$response = new LiResponse();
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
?>
