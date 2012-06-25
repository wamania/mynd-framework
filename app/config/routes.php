<?php

$routes = array(
	
    /********************************************************
     * Routes pour l'admin
     * (show, save, delete)
     * 
     ********************************************************/
	array(
		'url' 			=> '/admin/:model/:id',
		'params'		=> array(
			'controller' => 'admin',
			'action' => 'show',
			'model' => '[a-zA-Z0-9]*',
			'id' => '[0-9]*'
		)
	),
	
	/*array(
		'url'			=> '/admin/:model',
		'params'		=> array(
			'controller' => 'admin',
			'action' => 'index',
			'model' => '[a-zA-Z0-9]*'
		)
	),
	
	array(
		'url'			=> '/admin',
		'params'		=> array(
			'controller' => 'admin',
			'action' => 'index',
		)
	),*/
	
	
	array(
		'url'		=> '/admin/save',
		'params'	=> array(
			'controller' => 'admin',
			'action' => 'save',
		)
	),
	
	array(
		'url'		=> '/admin/delete/:id',
		'params'		=> array(
			'controller' => 'admin',
			'action' => 'delete',
			'id' => '[0-9]*'
		)
	),
	
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
	// Route vide
	array(
        'url' => '/',
        'params' => array(
			'module' => _c('default_module'),
			'controller'=> _c('default_controller'),
            'action'=> _c('default_action')
        ),
    ),
	
	array(
		'url' => '/:module/:controller/:action',
		'params' => array(
			'module'		=> '[a-zA-Z0-9\-]+',
			'controller' 	=> '[a-zA-Z0-9\-]+',
			'action'		=> '[a-zA-Z0-9\-]+'
		)
	),
	
	array(
		'url' => '/:controller/:action',
		'params' => array(
			'module' => _c('default_module'),
			'controller' 	=> '[a-zA-Z0-9\-]+',
			'action'		=> '[a-zA-Z0-9\-]+',
		)
	),
	
	// sans id
	array(
		'url' => '/:module', 
		'params' => array(
			'module' 		=> '[a-zA-Z0-9\-]+',
			'controller'	=> _c('default_controller'),
			'action'		=> _c('default_action'),
		)
	)
);

return $routes;
?>
