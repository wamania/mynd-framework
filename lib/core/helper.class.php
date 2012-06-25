<?php

class MfHelper {
    
    /**
     * Helper nous renvoyant un lien interne formatté selon l'url 
     * spécifiée dans la route la plus viable
     * @return $link String
     * @param $name Object
     * @param $params Object
     * @param $options Object[optional]
     */
    public static function link($name, $params, $options=array()) {
        
        $url = self::url($params);
        
        if (is_null($url)) {
            throw new Exception('Impossible de construire l\'url');
        }
        if (isset($options['anchor'])) {
           $url .= '#'.$options['anchor'];
           unset($options['anchor']);
        }
        $link = '<a href="'.$url.'" ';
        foreach ($options as $key=>$value) {
            $link .= $key.'="'.$value.'" ';
        }
        $link .= ' >'.$name.'</a>';
        
        return $link;
    }
    
    /**
     * Helper renvoyant l'url correspondante aux paramètres d'entrés
     * @return String $url
     * @param $params Array
     */
    public static function url(Array $params) {
        
        $urlEngineClassName = 'Mf'.ucwords(_c('url_handler')).'Url';
        $urlEngine = new $urlEngineClassName;

        return $urlEngine->params2url($params);
    }
    
    public static function urlTodomain($domain, Array $params) {
    	
    	$urlEngineClassName = 'Mf'.ucwords(_c('url_handler')).'Url';
        $urlEngine = new $urlEngineClassName;
    	
        $path = $urlEngine->params2path($params);
        
        return $urlEngine->path2url($path, $domain);
    }
    
    public static function paginate($paginator, $params, $options=array()) {
        
        if ( ! isset($options['separator'])) {
            $options['separator'] = '&nbsp;';
        }
        if ( ! isset($options['size'])) {
            $options['size'] = 2;
        }
       
        $first_page = $paginator['current'] - $options['size'];
        if ($first_page < 1) {
            $first_page = 1;
        }
        $last_page = $paginator['current'] + $options['size'];
        if ($last_page > $paginator['page_count']) {
            $last_page = $paginator['page_count'];
        }
       
        $links = array();
        
        // First
        if ($first_page > 1) {
            $links[] = self::link('<<', array_merge($params, array('page'=>1)), array('title'=>'Premi&egrave;re page'));
        }
        if ($first_page > 1) {
            $links[] = self::link('Pr&eacute;c&eacute;dent', array_merge($params, array('page'=>($paginator['current']-1))), array('title'=>'Page '.($paginator['current']-1)));
        }
        for ($i=$first_page; $i<=$last_page; $i++) {
            if ($i == $paginator['current']) {
                $links[] = $i;//'['.$i.']';
            } else {
                $links[] = self::link($i, array_merge($params, array('page'=>$i)), array('title'=>'Page '.$i));
            }
        }
        if ($last_page < $paginator['page_count']) {
            $links[] = self::link('Suivant', array_merge($params, array('page'=>($paginator['current']+1))), array('title'=>'Page '.($paginator['current']+1)));
        }
        if ($last_page < $paginator['page_count']) {
            $links[] = self::link('>>', array_merge($params, array('page'=>$paginator['page_count'])), array('title'=>'Derni&egrave;re page'));
        }
        
        $links = implode($options['separator'], $links);
        $links = '<div '.(!empty($options['class']) ? 'class="'.$options['class'].'"' : '').'>'.$links.'</div>';
        
        return $links;
    }
    
    /**
     * If <var>$text</var> is longer than <var>$length</var>, <var>$text</var> will 
     * be truncated to the length of <var>$length</var> and the last three characters
     * will be replaced with the <var>$truncate_string</var>.
     */
    public function truncate($text, $length = 30, $truncate_string = '...') {
        if (utf8_strlen($text) > $length)
            return utf8_substr_replace($text, $truncate_string, $length - utf8_strlen($truncate_string));
        else
            return $text;
    }
}

?>