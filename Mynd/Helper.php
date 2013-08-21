<?php

use Mynd\Core\Registery\Registery;

/**
 * Retourne une valeur du registre en fonction de la clé
 * @return mixed
 * @param $key String
 */
function _r($key)
{
    return Registery::get($key);
}

/**
 * Retourne une valeur du tableau config contenu dans le registre
 * @return mixed
 * @param $key String
 */
function _c($key)
{
    $cfg = Registery::get('config');
    if (is_array($cfg)) {
        if (isset($cfg[$key])) {
            return $cfg[$key];
        }
    }

    return null;
}