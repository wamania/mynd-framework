<?php

$routes = array(

	/********************************************************
	 *  Routes pour Auth
	 ********************************************************/
	array(
		'url' => '/login',
		'params' => array(
			'module'		=> 'default',
			'controller' => 'user',
			'action' => 'login'
		)
	),

	array(
		'url' => '/logout',
		'params' => array(
			'module'		=> 'default',
			'controller' => 'user',
			'action' => 'logout'
		)
	),

	array(
		'url' => '/register',
		'params' => array(
			'module'		=> 'default',
			'controller' => 'user',
			'action' => 'register'
		)
	),

	/********************************************************
	 * Routes par défaut
	 ********************************************************/
	array(
		'url' => '/:module/:controller/:action/:id',
		'params' => array(
			'module'	=> '[a-zA-Z0-9\-]+',
			'controller'=> '[a-zA-Z0-9\-]+',
			'action'	=> '[a-zA-Z0-9\-]+',
			'id' 		=> '[0-9]+'
		)
	),

	array(
		'url' => '/:module/:controller/:action',
		'params' => array(
			'module'	=> '[a-zA-Z0-9\-]+',
			'controller'=> '[a-zA-Z0-9\-]+',
			'action'	=> '[a-zA-Z0-9\-]+'
		)
	),

	array(
		'url' => '/:controller/:action',
		'params' => array(
			'module' 	=> _c('default_module'),
			'controller'=> '[a-zA-Z0-9\-]+',
			'action'	=> '[a-zA-Z0-9\-]+',
		)
	),

	array(
		'url' => '/:module',
		'params' => array(
			'module' 	=> '[a-zA-Z0-9\-]+',
			'controller'=> _c('default_controller'),
			'action'	=> _c('default_action'),
		)
	),

	array(
		'url' => '/',
		'params' => array(
			'module' 	=> _c('default_module'),
			'controller'=> _c('default_controller'),
			'action'	=> _c('default_action')
		),
	),
);

return $routes;
