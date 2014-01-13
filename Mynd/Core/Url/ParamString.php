<?php

namespace Mynd\Core\Url;

use Mynd\Core\Registery\Registery;

abstract class ParamString implements iUrlEngine
{
    public function url2params($url, $get)
    {
        $routes = Registery::get('routes');
        if (!is_array($routes)) {
            throw new Exception( "La liste des routes est vide.");
        }

        $tabUrl = explode('/', $url);

        foreach ($routes as $routeName => $route) {

            $routeUrl = substr($route['url'], 1, strlen($route['url'])-1);

            // 1er test, le nombre de params
            $tabRouteUrl = explode('/', $routeUrl);
            if (count($tabRouteUrl) != count($tabUrl)) {
                continue;
            }

            // ensuite, test si la regexp de l'url est bonne
            preg_match_all("/:([a-zA-Z0-9_]*)/", $routeUrl, $neededParams);
            $url_regexp = $routeUrl;
            foreach ($neededParams[1] as $p) {
                // On cherche si une regexp est definit dans la route
                $regexp = (isset($route['params'][$p]) ? $route['params'][$p] : '[a-zA-Z0-9_\-]*');
                $regexp = '(?P<'.$p.'>'.$regexp.')';
                $url_regexp = preg_replace("/(:".$p.")/", $regexp, $url_regexp);

            }
            $url_regexp = '#^'.$url_regexp.'$#i';
            if (preg_match($url_regexp, $url, $matches)) {
                foreach($matches as $key => $match) {
                    if (is_int($key)) {
                        unset($matches[$key]);
                    }
                }
                if (! isset($route['params'])) {
                    $route['params'] = array();
                }
                unset($get['ps']);

                return array(
                    'route_name' => $routeName,
                    'params' => array_merge($route['params'], $matches, $this->array_urldecode($get))
                );
            }
        }

        return $this->array_urldecode($get);
    }

    public function params2path($params)
    {
        if (empty($params['controller'])) {
            $params['controller'] = _c('default_controller');
        }
        if (empty($params['action'])) {
            $params['action'] = _c('default_action');
        }
        if (empty($params['module'])) {
            $params['module'] = _c('default_module');
        }

        $routes = Registery::get('routes');
        $url = null;

        foreach ($routes as $route) {

            $badparams = false;

            preg_match_all("/:([a-zA-Z0-9_]*)/", $route['url'], $neededParams);
            if (empty($route['params'])) {
                $route['params'] = array();
            }
            // On vérifie pour chaque variable de l'url que la params correspondant bien
            // à ce qu'on a spécifié dans le tableau params
            $ValueParamsURL = array();
            foreach ($neededParams['1'] as $p) {

                if (isset($params[$p])) {

                    if (isset($route['params'][$p])) {
                        $regexp = $route['params'][$p];
                    } else {
                        $regexp = '[a-zA-Z0-9_]*';
                    }
                    if (preg_match('#'.$regexp.'#i', $params[$p])) {
                        $ValueParamsURL[$p] = $params[$p];

                        // Le params initiale ne correspond pas à la regexp donnée par la route
                    } else {
                        $badparams = true;
                    }

                    // le params existe dans l'url, mais pas de le tableau initial
                } else {
                    $badparams = true;
                }
            }

            if ($badparams) {
                continue;
            }

            // On regroupe les paramètres de l'url et ceux de la route
            $tabParamsURL = array_merge($route['params'], $ValueParamsURL);

            // On vérifie qu'on a bien tous nos paramètres !
            foreach ($tabParamsURL as $key => $value) {

                if ( (!isset($params[$key])) || ($value != $params[$key]) ) {
                    $badparams = true;
                }
            }
            if ($badparams) {
                continue;
            }

            // On a notre route !
            $finalTabParams = $route;
            $finalNeededParams = $neededParams['1'];
            $finalParamsURL = $tabParamsURL;

            break;
        }

        // on vire le module si on l'a rajouté au début (pour eviter une erreur sur l'indexe null)
        if (is_null($params['module'])) {
            unset($params['module']);
        }

        // On a notre route
        // On met les paramètres de l'url dans l'url
        $url = $finalTabParams['url'];
        foreach ($finalNeededParams as $p) {
            $url = str_replace(':'.$p, $params[$p], $url);
        }
        // On ajoute les ?key=value en regardant ce qui n'a pas encore été mis
        $getParams = array_diff_key($params, $finalParamsURL);
        if (count($getParams) > 0) {
            $tabTempurl = array();
            foreach ($getParams as $key=>$value) {
                if (isset($value)) {
                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $tabTempurl[] = $key.'[]='.urlencode($v);
                        }
                    } else {
                        $tabTempurl[] = $key.'='.urlencode($value);
                    }
                }
            }
            if (count($tabTempurl)) {
                $url .= '?';
                $url .= implode('&', $tabTempurl);
            }
        }

        return $url;
    }

    protected function array_urldecode($tab)
    {
        $new_tab = array();

        foreach ($tab as $key => $value) {

            if (is_array($value)) {
                $new_tab[$key] = self::array_urldecode($value);
            } else {
                $new_tab[$key] = urldecode($value);
            }
        }
        return $new_tab;
    }
}
