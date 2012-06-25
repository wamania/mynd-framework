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
function _a ($name, $selector, $params=array(), $options=array()) {
	
	if (is_array($selector)) {
		$params = array_merge($params, $selector);
	} else {
		$tabSelector = explode(':', $selector);
		if (count($tabSelector) != 2) {
			throw new Exception('Mauvais selecteur dans le lien');
		}
		$params['controller'] = $tabSelector[0];
		$params['action'] = $tabSelector[1];
	}
	
	return LiHelper::link($name, $params, $options);
}

function _selector($query, $requestParams = array())
{
	// sous la forme complet :    module:controller:action|id=8;user_id=9
	$tabQuery = explode('|', $query);
	
	if ( empty($tabQuery[0])) {
		throw new Exception('Mauvais selecteur dans le lien '.$params);
	}
	
	if (empty($requestParams['module'])) {
		$requestParams['module'] = _c('default_module');
	}
	if (empty($requestParams['controller'])) {
		$requestParams['controller'] = _c('default_controller');
	}
	
	$tabParams = explode(':', $tabQuery[0]);
	$params = array();
	
	if (count($tabParams) == 1) {
		$params['module'] = $requestParams['module']; // retrocompatibilité
	    $params['controller'] = $requestParams['controller'];
	    $params['action'] = $tabParams[0];
	    //$url = _url($params);
	
	} elseif (count($tabParams) == 2) {
		$params['module'] = $requestParams['module']; // retrocompatibilité
	    $params['controller'] = $tabParams[0];
	    $params['action'] = $tabParams[1];
	    //$url = _url($params);
	
	} elseif (count($tabParams) == 3) {
		$params['module'] =  $tabParams[0];
	    $params['controller'] = $tabParams[1];
	    $params['action'] = $tabParams[2];
	    //$url = _url($params);

	} else {
	    throw new Exception('Mauvais selecteur dans le lien');
	}
	
	if (!empty($tabQuery[1])) {
		$tabVars = explode(';', $tabQuery[1]);
		if (!empty($tabVars)) {
			foreach ($tabVars as $var) {
				$tabVar = explode ('=', $var);
				if ( ($tabVar[0] != 'module') && ($tabVar[0] != 'controller') && ($tabVar[0] != 'action') ) {
					$params[$tabVar[0]] = $tabVar[1];
				}
			}
		}
	}
	
	return $params;
}

function _paginate($paginator, $params, $options=array())
{
	return LiHelper::paginate($paginator, $params, $options=array());
}

/**
 * Obtenir très vite une url pour les paramètres donnés
 * @return String URL
 * @param $params Object
 */
function _url($params) 
{
	return _urlToDomain(null, $params);
}

function _urlToDomain($domain, $params)
{
	if (is_array($params)) {
		return LiHelper::urlTodomain($domain, $params);
		
	} elseif (is_string($params)) {
		// si c'est simplement une url...
		if (preg_match('#^http://#i', $params)) {
			return $params;
		}
		
		return LiHelper::urlTodomain($domain, _selector($params));
	}
}

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
	return LiRegistery::get($key);
}

/**
 * Retourne une valeur du tableau config contenu dans le registre
 * @return mixed
 * @param $key String
 */
function _c($key) 
{
	$cfg = LiRegistery::get('config');
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
function _date($date, $format='%d/%m/%Y', $time=false)
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
}

/**
 * Change une date en jj/mm/yyyy en yyyy-mm-dd
 * Enter description here ...
 * @throws Exception
 */
function _frDateToMySQL($date)
{
	$tabDate = explode('/', $date);
	$oDate = new SDate($tabDate[2], $tabDate[1], $tabDate[0]);
	return $oDate->format('%F');
}

/*function _paginate($query, $params, $per_page=10, $size=3) {
	$paginator = new SPaginator($query, $per_page, 1, 'page');
	
	return LiHelper::paginate($paginator, $params, $options=array('size'=>$size));
}*/

function public_path() 
{
	return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).LI_APP_NAME.'public';
}
function files_path()
{
	return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']).'files';
}
function _img($path) 
{
	return public_path().'/images/'.$path;
}

function _csspath($path)
{
	return public_path().'/styles/'.$path;
}

function _css($path) 
{
	return '<link rel="stylesheet" href="'._csspath($path).'" media="all"/>';
}

function _file($path) 
{
	return files_path().'/'.$path;
}

function _jspath($path)
{
	return public_path().'/scripts/'.$path;
}

function _js($path) 
{
	return '<script type="text/javascript" src="'._jspath($path).'"></script>';
}

function _flash($path) 
{
	return public_path().'/flash/'.$path;
}
