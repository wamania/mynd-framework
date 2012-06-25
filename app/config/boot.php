<?php

/**
 * Fonction onBoot, lancée automatiquement par le framework juste après la résolution
 * de l'url, mais avant d'appeler le couple controller/action
 * @param $request
 * @param $response
 * @param $config
 * @return unknown_type
 */
function onBoot(&$request, &$config)
{
	if (empty($config['domain'])) {
		return false;
	}
	
	// pour faire fonctionner les sessions dans les sous-domaines
	session_set_cookie_params ( 0, '/', '.'.$config['domain'] );
	
	$params = &$request->getParams();
	
	// on donne à notre model la connexion à la base tendance
	//LiModel::setDb(_r('db_tendance'));
	if (empty($params['module'])) {
		$params['module'] = $config['default_module'];
	}
	if (empty($params['controller'])) {
		$params['controller'] = $config['default_controller'];
	}
	if (empty($params['action'])) {
		$params['action'] = $config['default_action'];
	}
	
	// si c'est login ou register en jajax, on laisse filer sur le site de tendance
	if ($params['controller'] == 'user') {
		return;
	}
	
	// quelques fonctions qu'on laisse filer sur le front
	// histoire de pas les avoir en double (front + profil revendeur)
	if ( ($params['controller'] == 'default') && ($params['action'] == 'produitByFamille') ) {
		return;
	}
	if ( ($params['controller'] == 'default') && ($params['action'] == 'setDisplayOptions') ) {
		return;
	}
	if ( ($params['controller'] == 'default') && ($params['action'] == 'page') ) {
		return;
	}
	if ( ($params['controller'] == 'default') && ($params['action'] == 'suggest') ) {
		return;
	}
	
	// si on va sur l'espace revendeur ou admin es, idem, on laisse filer
	/*if ( ($params['module'] == 'espace-distributeur') || ($params['module'] == 'espace-es') || ($params['module'] == 'espace-marque') ) {
		return;
	}*/

	// si c'est un sous domain de tendance
	if ( ($config['domain'] != $_SERVER['HTTP_HOST']) && ('www.'.$config['domain'] != $_SERVER['HTTP_HOST']) ) {
		
		// ici, on part vers le module export, rien à faire du reste
		if ( ($_SERVER['HTTP_HOST'] == $config['export']) || ($_SERVER['HTTP_HOST'] == 'www.'.$config['export']) ) {
			$params['module'] = 'export';
			if ($params['controller'] == 'default') {
				$params['controller'] = 'produits';
				$params['action'] = 'export';
			}
			
			// pour faire fonctionner les sessions dans le domain de l'export
			session_set_cookie_params ( 0, '/', '.'.$config['export'] );
		}
		
		// si module front : on appelle le module front, on redirige donc vers le front
		if ($params['module'] == 'tendance2') {
			// cas particulier, si racine d'un espace revendeur, on reste sur l'espace revendeur
			// pour pas être redirigé vers le front, car dans les routes, 
			// la racine appartient au front uniquement
			if ( ($params['controller'] == 'default') && ($params['action'] == 'index') ) {
				$params['module'] = 'revendeur2';
			
			// sinon c'est qu'on veut aller sur le front
			} else {
				header('Location: '._urlToDomain(_c('domain'), $params));
				die();
			}
		
		}
	}

	// sinon, si on detecte un sous-domaine ou domaine, on redirige vers le module revendeur
	/*if ( ($config['domain'] != $_SERVER['HTTP_HOST']) && ('www.'.$config['domain'] != $_SERVER['HTTP_HOST']) ) {

		$params['module'] = 'revendeur';
	}*/
}