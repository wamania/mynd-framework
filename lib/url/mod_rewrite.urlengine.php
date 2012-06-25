<?php

class LiModrewriteUrl extends LiParamString implements iUrlEngine {
	
    public function path2url($path, $domain = null) {
    	
    	if (is_null($domain)) {
    		$domain = $_SERVER['SERVER_NAME'].str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    	}
        return 'http://'.$domain.$path;
    }
    
    public function params2url($params) {
        $path = $this->params2path($params);
        return $this->path2url($path);
    }
}
?>
