<?php
/**
 * 
 */
class MfResponse {
	
	public $headers;
	
	public $body;
	
	public function __construct() {
		$this->headers = array();
		$this->body = '';
	}
	
	public function headers($headers) {
		if (is_array($headers)) {
			$this->headers = array_merge($this->headers, $headers);
		} else {
			$this->headers[] = $headers;
		}
	}
	
	public function out() {
		if (empty($this->headers)) {
			$this->headers('HTTP/1.0 200 OK');
		}
		foreach ($this->headers as $h) {
			header($h);
		}
		echo $this->body;
		die();
	}
	
	public function redirect($url) {
		$this->headers('Status: 302 Found');
		$this->headers('Location: '.$url);
		$this->body = 'Redirection vers : '.$url;
		$this->out();
	}
	
	public function send404() {
		$this->headers('HTTP/1.0 404 Not Found');
		$this->body = 'Erreur 404';
		$this->out();
	}
}
?>
