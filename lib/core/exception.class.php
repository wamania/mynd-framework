<?php

//set_exception_handler('exception_handler');

/*function exception_handler($e) {
	try {
		throw new MfException($e->getMessage(), $e->getCode());
	
	} catch (MfException $ye) {
		$ye->noCatchedException($e);
		echo $ye->out();
	}
}*/

class MfException extends Exception {
	
	private $originalException;
	
	public function noCatchedException ($e) {
		$this->originalException = $e;
	}
	
	public function out() {
		echo '<div style="margin:5px;padding:3px;background-color:#D0E0FF;border:dashed 1px red;font-size:14px;">';
		if (!empty($this->originalException)) {
			echo 'Exception non captur&eacute;e :'.get_class($this->originalException).'<br /><br />';
		} else {
			echo 'yException : <br /><br />';
		}
		echo 'yException : '.$this->getMessage().'<br />Fichier : '.$this->getFile().'  / Ligne : '.$this->getLine().'<br /><br />';
		if (!empty($this->originalException)) {
			echo '<br /><br />Exception originale : '.$this->originalException->getMessage().'<br />Fichier : '.$this->originalException->getFile().'  / Ligne : '.$this->originalException->getLine().'<br /><br />';
		}
		echo '<pre>Trace : '.$this->getTraceAsString().'</pre>';
		
		if (!empty($this->originalException)) {
			echo '<br /><br />Trace originale : <pre>'.$this->originalException->getTraceAsString().'</pre>';
		}
		echo '</div>';
		die();
	}
}

?>
