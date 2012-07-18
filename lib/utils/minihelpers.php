<?php

/**
 * Un helper alias de yHelper::link
 *
 * TODO : a refaire, c'est nul
 *
 * @return String <a href="($params)" ($options)>$name</a>
 * @param $name String
 * @param $selector String "app:controller:action"
 * @param $params Array[optional]
 * @params $options Array[optional]
 */

function _paginate($paginator, $params, $options=array())
{
    return MfHelper::paginate($paginator, $params, $options=array());
}

/**
 * TODO : gestion des accents
 * @param unknown_type $string
 */
function _urlize($string)
{
    return preg_replace('#([^0-9a-zA-Z\.])#', '-', $string);
}

/**
 * Retourne une valeur du registre en fonction de la clé
 * @return mixed
 * @param $key String
 */
function _r($key)
{
    return MfRegistery::get($key);
}

/**
 * Retourne une valeur du tableau config contenu dans le registre
 * @return mixed
 * @param $key String
 */
function _c($key)
{
    $cfg = MfRegistery::get('config');
    if (is_array($cfg)) {
        if (isset($cfg[$key])) {
            return $cfg[$key];
        }
    }

    return null;
}

/**
 * Params time obsolete
 *
 * Enter description here ...
 * @param unknown_type $date
 * @param unknown_type $format
 * @param unknown_type $time
 */
/*function _date($date, $format='%d/%m/%Y', $time=false)
{
    if ($time) {
        $oDate = SDateTime::parse($date);
    } else {
        $oDate = SDate::parse($date);
    }

    return $oDate->format($format);
}

function _datetime($datetime, $format='%d/%m/%Y %R')
{
    $oDatetime = SDateTime::parse($datetime);
    return $oDatetime->format($format);
}*/

/**
 * Change une date en jj/mm/yyyy en yyyy-mm-dd
 * Enter description here ...
 * @throws Exception
 */
/*function _frDateToMySQL($date)
{
    $tabDate = explode('/', $date);
    $oDate = new SDate($tabDate[2], $tabDate[1], $tabDate[0]);
    return $oDate->format('%F');
}*/
function rootPath()
{
    return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
}
function wwwPath()
{
    return rootPath().'www';
}
function _img($path)
{
    return wwwPath().'/images/'.$path;
}

function _cssPath($path)
{
    return wwwPath().'/css/'.$path;
}

function _css($path)
{
    return '<link rel="stylesheet" href="'._cssPath($path).'" media="all"/>';
}

function _file($path)
{
    return wwwPath().'/files/'.$path;
}

function _jsPath($path)
{
    return wwwPath().'/js/'.$path;
}

function _js($path)
{
    return '<script src="'._jsPath($path).'"></script>';
}
