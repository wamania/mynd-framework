<?php

$config = array
(
    'local' => array
    (
        // 'modrewrite', 'multiviews', 'querystring', 'simple'
        'url_handler' => 'modrewrite',

        // 'orm', 'simple'
        'model' => 'simple',

        // Routes par défaut
        'default_module' => 'generator',
    ),
    'development' => array
    (
        // 'modrewrite', 'multiviews', 'querystring', 'simple'
        'url_handler' => 'modrewrite',

        // 'orm', 'simple'
        'model' => 'simple',

        // Routes par défaut
        'default_module' => 'generator',
    ),
    'production' => array
    (
        // 'modrewrite', 'multiviews', 'querystring', 'simple'
        'url_handler' => 'modrewrite',

        // 'orm', 'simple'
        'model' => 'simple',

        // Routes par défaut
        'default_module' => 'generator',
    )
);

return $config[_r('environment')];
