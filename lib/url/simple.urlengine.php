<?php

class MfSimpleUrl implements iUrlEngine {

	public function url2params($url, $get) {

		$params = array();
		if ( (!isset($get['controller'])) && (!isset($get['action'])) ) {
			$get['module'] = _c('default_module');
			$get['controller'] = _c('default_controller');
			$get['action'] = _c('default_action');

		}
		if ( (isset($get['controller'])) && (isset($get['action']))) {
			$params['module'] = $get['module'];
			$params['controller'] = $get['controller'];
			$params['action'] = $get['action'];
				
			foreach ($get as $key => $value) {
				$params[$key] = urldecode($value);
			}
		}

		return $params;
	}

	public function params2path($params) {

		if (empty($params['controller'])) {
			$params['controller'] = _c('default_controller');
		}
		if (empty($params['action'])) {
			$params['action'] = _c('default_action');
		}
		if (empty($params['module'])) {
			$params['module'] = null;
		}

		$url_vars = array();
		foreach ($params as $key => $value) {
			$url_vars[] = $key.'='.urlencode($value);
		}
		$url_vars = implode('&', $url_vars);

		return $url_vars;

	}

	public function path2url($path) {
		return 'http://'
		.$_SERVER['SERVER_NAME']
		.$_SERVER['SCRIPT_NAME']
		.'?'.$path;
	}

	public function params2url($params) {
		$path = $this->params2path($params);
		return $this->path2url($path);
	}
}
?>