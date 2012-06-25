<?php

$config = array
(
	'development' => array
	(
		// 'modrewrite', 'multiviews', 'querystring', 'simple'
		'url_handler' => 'modrewrite',
	
		// 'orm', 'simple'
		'model' => 'orm',
		
		// Routes par défaut
		'default_module' => 'default',
		'default_controller' => 'default',
		'default_action' => 'index',
		
		'cache' => 'fakecache',
		'cache_options' => array(
		)
	),
	'test' => array
	(
		// Possibles are : 'modrewrite', 'multiviews', 'querystring', 'simple'
		'url_handler' => 'modrewrite',
	
		// 'orm', 'simple'
		'model' => 'orm'
	),
	'production' => array
	(
		// Possibles are : 'modrewrite', 'multiviews', 'querystring', 'simple'
		'url_handler' => 'modrewrite',
	
		// 'orm', 'simple'
		'model' => 'orm',
		
		'cache' => 'memcache',
		'cache_options' => array(
			'servers' => array(
				//array('178.33.249.156', 11211),
				array('127.0.0.1', 11211)
			)
		)
	)
);

return $config[_r('environment')];

?>
