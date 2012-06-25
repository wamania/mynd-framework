<?php

$config = array
(
    'development' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=lithium',
		'user' => 'root',
		'pass' => 'equinoxe2007'
    ),
    'production' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=lithium',
		'user' => 'lithium',
		'pass' => 'meuhmeuh'
    ),
    'test' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=framework',
		'user' => 'root',
		'pass' => 'meuhmeuh'
    )
);


return $config[_r('environment')];

?>
