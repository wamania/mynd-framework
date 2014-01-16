<?php

namespace Mynd\Core\Response;

/**
 *
 */
class Response
{
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

    public function redirect($url, $options = array())
    {
        $status = 'Status: 302 Found';
        if (! empty($options['code'])) {
            switch ($options['code']) {
                case 300:
                    $status = '300 Multiple Choices';
                    break;
                case 301:
                    $status = '301 Moved Permanently';
                    break;
                case 302:
                    $status = '302 Found';
                    break;
                case 303:
                    $status = '303 See Other';
                    break;
                case 304:
                    $status = '304 Not Modified';
                    break;
                case 305:
                    $status = '305 Use Proxy';
                    break;
                case 306:
                    $status = '306 Switch Proxy';
                    break;
                case 307:
                    $status = '307 Temporary Redirect';
                    break;
                case 308:
                    $status = '308 Permanent Redirect';
                    break;
            }
        }
        $this->headers($status);
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
