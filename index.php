<?php

define ('LI_ROOT',    dirname(__FILE__).'/');

define ('LI_APP',    LI_ROOT.'app/');

define ('LI_LIB',     LI_ROOT.'lib/');
define ('LI_MODEL',   LI_ROOT.'orm/');

// Librairie du framework
require_once LI_LIB.'index.php';

// L'ORM
require_once LI_MODEL.'index.php';

/**
 * Récupèration du contrôleur, de l'action et des paramétres
 * Puis, lancement de l'action
 */

LiBoot::init();
