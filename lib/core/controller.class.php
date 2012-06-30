<?php

/**
 * Classe abstraite commune a tous les contrôleurs
 * Définit le modèle et la vue
 */

abstract class MfController {

    /**
     * L'objet supportant la requete
     */
    protected $request;

    /**
     * L'objet supportant la réponse
     */
    protected $response;

    /**
     * Les paramétres
     */
    protected $params;

    /**
     * La session
     */
    protected $session;

    /**
     * Notre vue
     */
    protected $view;

    /**
     * Les données stockées ici qui seront envoyées a la vue
     */
    protected $data;

    /**
     * Nos filtres (before et/ou after)
     */
    protected $filter;

    /**
     * Passage d'une info d'une page à la suivante, par exemple pour indiquer un success ou une error à afficher
     */
    private $notifier;

    /**
     * tableau params de la requete precedente
     */
    protected $lastParams;

    /**
     * Prochaine requete si on souhiate terminer l'action actuelle
     * Enter description here ...
     * @var unknown_type
     */
    protected $nextParams;

    /**
     * Rendu déjà fait ?
     * @return
     */
    private $isRendered;

    /**
     * Tableau d'erreur
     * Enter description here ...
     * @var array
     */
    protected $jsErrors;

    protected $jsSuccess;

    protected $cache = null;

    /**
     * Tableau des actions à afficher avec layout
     * @return
     */
    protected $actions_with_layout = array();

    /**
     * Constructeur
     */
    public function __construct($request, $response) {

        $this->isRendered = false;

        $this->session = new MfPHPSession;
        $this->session->start();

        $this->request = $request;
        $this->params = $this->request->getParams();

        $this->response = $response;

        $this->jsErrors = array();
        $this->jsSuccess = array();

        $this->notifier = null;
        $this->nextParams = null;

        if (!empty($this->session['li:notifier'])) {
            $this->notifier = $this->session['li:notifier'];
            unset($this->session['li:notifier']);
        }

        $this->lastParams = $this->session['lastParams'];

        $this->view = new MfView($this);

        // cache
        $cache = _c('cache');
        if (empty($cache)) {
            $cache = 'MfFakecache';
        } else {
            $cache = 'Mf'.ucwords($cache);
        }

        $this->cache = new $cache;

        $cacheOptions = _c('cache_options');
        if (!empty($cacheOptions)) {
            $this->cache->setOptions($cacheOptions);
        }
    }

    /**
     * Setter et getter
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Isset et unset
     */
    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    /**
     * Send notifier for next page
     * @param $code : what you want, 'success' or 'error' for example
     * @param $msg : message
     */
    protected function notify($code, $msg, $now=false)
    {
        if ($now) {
            $this->notifier = array( 'code' => $code, 'msg' => $msg);
        } else {
            $this->session['li:notifier'] = array( 'code' => $code, 'msg' => $msg);
        }
    }

    /**
     * Get the notifier for current page
     */
    public function &getNotifier()
    {
        return $this->notifier;
    }

    public function &getParams()
    {
        return $this->params;
    }

    protected function jsError($msg = null)
    {
        $this->jsErrors[] = $msg;
    }

    protected function jsSuccess($msg = null, $datas = array())
    {
        $this->jsSuccess[] = array('msg' => $msg, 'datas' => $datas);
    }

    public function Getparam($index, $default = null)
    {
        if (!empty($this->params[$index])) {
            return $this->params[$index];
        }
        if ( ! is_null($default)) {
            return $default;
        }

        return null;
    }

    /**
     *
     * @return
     * @param $params Object
     */
    public function render_action($template = null) {

        // On envoi les erreurs au js
        if ( ! empty($this->jsErrors)) {
            $render = new stdClass();
            $render->code = 'error';
            $render->msg = implode('<br />', $this->jsErrors);
            $this->render_text(json_encode($render));
            return true;
        }

        // on envoi le success au js
        if ( ! empty($this->jsSuccess)) {
            $render = new stdClass();
            $render->code = 'success';

            $msgs = array();
            $datas = array();
            foreach ($this->jsSuccess as $s) {
                $msgs[] = $s['msg'];
                $datas = array_merge($datas, $s['datas']);
            }
            $render->msg = implode('<br />', $msgs);
            $render->datas = $datas;
            $this->render_text(json_encode($render));
            return true;
        }

        if (is_null($template)) {
            $template = array(
                'module' => $this->params['module'],
                'controller' => $this->params['controller'],
                'action' => $this->params['action']
            );
        }

        $layout_name = null;
        if (array_key_exists($this->params['action'], $this->actions_with_layout)) {
            if (!is_null($this->actions_with_layout[$this->params['action']])) {
                $layout_name = $this->actions_with_layout[$this->params['action']];
            }
        } elseif (array_key_exists('default', $this->actions_with_layout)) {
            $layout_name = $this->actions_with_layout['default'];
        }
        if (!is_null($layout_name)) {
            $this->data['layout_content'] = $body = $this->view->render($template, $this->data);

            $layout = _selector($layout_name, array(
                'module' => $this->params['module'],
                'controller' => 'layout')
            );

            //$body = $this->render_getfilecontent($layout);
            $body = $this->view->render($layout, $this->data);
        } else {
            //$body = $this->render_getfilecontent($template);
            $body = $this->view->render($template, $this->data);
        }
        $this->finalize_render($body);
    }

    public function render($template)
    {
        $body = $this->view->render($template, $this->data);
        $this->finalize_render($body);
    }


    // a revoir
    /*public function render_data($data, $headers=array()) {
    $defaults = array
    (
            'type' => 'application/octet-stream',
            'disposition' => 'attachment',
    );
    $headers = array_merge($defaults, $headers);

    $this->response->headers($headers);

    if (is_string($data)) {
    $data = fopen($data, 'r');
    }

    if (is_resource($data))
    {
    $this->response->send_headers();
    rewind($data);
    fpassthru($data);
    exit();
    }
    $this->isRendered = true;
    $this->finalize_render($data);
    }*/

    public function render_file($file, $filename, $force = true)
    {
        $tab = array();
        if (preg_match('#.*\.([a-zA-Z]{2,4})$#', $filename, $tab)) {
            $extension = $tab[1];
            switch($extension) {

                case 'docx':
                    $mimetype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    $force = false;
                    break;

                case 'xlsx':
                    $mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    $force = false;
                    break;

                case 'pptx':
                    $mimetype = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                    $force = false;
                    break;

                case 'ppsx':
                    $mimetype = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
                    $force = false;
                    break;
            }
        }

        if (empty($mimetype)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $file);
        }

        if ($force) {
            $this->response->headers(array ('Content-disposition: attachment; filename='.$filename));
        } else {
            $this->response->headers(array ('Content-disposition: inline; filename='.$filename));
        }
        $this->response->headers(array (
                'Content-Type: '.$mimetype,
                'Pragma: no-cache'
        ));

        $datas = file_get_contents($file);
        $this->finalize_render($datas);
    }

    public function render_text($txt) {

        $this->finalize_render($txt);
    }

    private function finalize_render($str, $headers=array()) {

        $this->response->headers = array_merge($this->response->headers, $headers);
        $this->response->body = $str;

        $this->isRendered = true;
    }

    protected function no_render()
    {
        $this->isRendered = true;
    }

    /**
     * Execute nos filtres et notre action
     * @return void
     */
    public function runAction($action) {

        //L'historique des urls appelées
        if (!is_array($this->session['history'])) {
            $this->session['history'] = array( 'current'=> array() );
        }
        // Variable local car la méthode magique __set interdit l'utilisation de tableau 2D
        $history = $this->session['history'];
        //$history_diff = array_diff_assoc(_r('params'), $history['current']);
        if ( _r('params') !== $history['current']) {
            $this->session['history'] = array('current' => _r('params'), 'last' => $history['current']);
        }

        // appel de init dans le controller, s'applique à toutes les actions
        if (method_exists($this, 'init')) {
            $this->init();
        }

        // Les filtres befores
        if (isset($this->filter['before']) && (is_array($this->filter['before']))) {
            foreach ($this->filter['before'] as $key => $value) {
                if (in_array($action, $value)) {
                    if ( ! method_exists($this, $key)) {
                        throw new Exception ('Filtre before '.$key.' inextistant');
                    } else {
                        $this->$key();
                    }
                }
            }
        }

        // Notre action principale
        if ( ! method_exists($this, $action)) {

            throw new Exception ('Action '.$action.'introuvable');
        }
        $this->$action();

        // on redirige vers un autre jeu de params
        if (!is_null($this->nextParams)) {
            return;
        }

        // Nos filtres after
        if (isset($this->filter['after']) && (is_array($this->filter['after']))) {
            foreach ($this->filter['after'] as $key => $value) {
                if (in_array($action, $value)) {
                    if ( ! method_exists($this, $key)) {
                        throw new Exception ('Filtre after '.$key.' inextistant');
                    } else {
                        $this->$key();
                    }
                }
            }
        }

        // appel de finish dans le controller, s'applique à toutes les actions
        if (method_exists($this, 'finish')) {
            $this->finish();
        }

        $this->session['error'] = '';

        if (!$this->isRendered) {
            $this->render_action();
        }

        $this->session['lastParams'] = $this->params;
        $this->session->stop();

        return $this->response;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $selector
     */
    public function next($params)
    {
        $this->nextParams = $params;
    }

    public function getNext()
    {
        if (is_null($this->nextParams)) {
            return null;
        }
        $request = new MfRequest();
        $request->setParams($this->nextParams);
        return $request;
    }

    /**
     * Notre redirecteur
     * @return
     * @param $params Object
     */
    public function redirect_to($params) {
        // Tableau de params, dont controller et action
        if (is_array($params)) {
            if ( empty($params['controller'])) {
                $params['controller'] = $this->params['controller'];
            }
            if ( empty($params['action'])) {
                throw new Exception ('Vous devez préciser l\'action vers laquelle vous redirigez la page');
            }
            $url = _url($params);
            if (is_null($url)) {
                throw new Exception ('Impossible de trouver une url correspondante aux paramètres');
            }
            // Url
        } else if (is_string($params)) {
            $url = _url($params);
        }

        $this->response->redirect($url);
    }
}
