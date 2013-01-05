<?php

// sous la forme complet :    module:controller:action|id=8&user_id=9
function _selector($query)
{
    // requête actuelle (pour compléter les éléments manquants
    $params = _r('params');
    $queryParams = array();

    $tabQuery = explode('|', $query);
    $tabParams = explode(':', $tabQuery[0]);

    if (count($tabParams) == 0) {
        $queryParams['module'] = $params['module'];
        $queryParams['controller'] = $params['controller'];
        $queryParams['action'] = $params['action'];

    } elseif (count($tabParams) == 1) {
        $queryParams['module'] = $params['module'];
        $queryParams['controller'] = $params['controller'];
        $queryParams['action'] = $tabParams[0];

    } elseif (count($tabParams) == 2) {
        $queryParams['module'] = $params['module'];
        $queryParams['controller'] = $tabParams[0];
        $queryParams['action'] = $tabParams[1];

    } elseif (count($tabParams) == 3) {
        $queryParams['module'] =  $tabParams[0];
        $queryParams['controller'] = $tabParams[1];
        $queryParams['action'] = $tabParams[2];

    } elseif (count($tabParams) == 4) {
        $queryParams['module'] =  $tabParams[0];
        $queryParams['controller'] = $tabParams[1];
        $queryParams['action'] = $tabParams[2];
        $queryParams['id'] = $tabParams[3];

    } else {
        throw new Exception('Mauvais selecteur dans le lien');
    }

    if (!empty($tabQuery[1])) {
        $tabVars = explode('&', $tabQuery[1]);
        if (!empty($tabVars)) {
            foreach ($tabVars as $var) {
                $tabVar = explode ('=', $var);
                if ( ($tabVar[0] != 'module') && ($tabVar[0] != 'controller') && ($tabVar[0] != 'action') ) {
                    $queryParams[$tabVar[0]] = $tabVar[1];
                }
            }
        }
    }

    return $queryParams;
}

/**
 * Helper renvoyant l'url correspondante aux paramètres d'entrés
 * @return String $url
 * @param $params Array
 */
function _url($params, $domain = null)
{
    if (is_string($params)) {
        // si c'est simplement une url...
        if (preg_match('#^http://#i', $params)) {
            return $params;
        } else {
            $params = _selector($params);
        }
    }

    $urlEngineClassName = 'Mf'.ucwords(_c('url_handler')).'Url';
    $urlEngine = new $urlEngineClassName;

    if (!is_null($domain)) {
        $path = $urlEngine->params2path($params);
        return $urlEngine->path2url($path, $domain);
    }

    return $urlEngine->params2url($params);
}
