<?php

$config = array
(
    'development' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=myndframework',
		'user' => 'root',
		'pass' => 'password'
    ),
    'production' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=myndframework',
		'user' => 'root',
		'pass' => 'password'
    ),
    'test' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=myndframework',
		'user' => 'root',
		'pass' => 'password'
    )
);


return $config[_r('environment')];
