<?php

$config = array
(
    'local' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=myndframework',
        'user' => 'root',
        'pass' => 'password'
    ),
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
);


return $config[_r('environment')];
