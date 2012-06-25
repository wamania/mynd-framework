<?php
interface iUrlEngine {
	
	function url2params( $url,  $get );
	
	function params2path( $params );
    
    function path2url( $path );
}
?>
