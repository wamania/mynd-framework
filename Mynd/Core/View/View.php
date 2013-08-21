<?php
/**
 * Notre classe Vue
 */
namespace Mynd\Core\View;

use Mynd\Core\Url\Url;

class View
{
    /**
     * Notre tableau de données provenant de la vue
     */
    private $data;

    /**
     * Les GET et POST
     * @var Array
     */
    private $params;

    /**
     * Notification to display
     * @var unknown_type
     */
    //private $notifier;

    private $controller;

    /**
     * Constructor
     * @param $params Array
     * @return unknown_type
     */
    public function __construct(&$controller)
    {
        $this->controller = $controller;

        $this->params = &$controller->getParams();
        //$this->notifier = &$controller->getNotifier();
    }

    public function params($index, $default = null)
    {
        return $this->controller->getParam($index, $default);
    }

    /**
     * Fonction principale pour l'affichage "standard"
     * @return void
     * @param $data Array
     * @param $params Array
     */
    public function render( $template, $data = array() )
    {

        if ( ! is_array($template)) {
            $template = _selector($template, array(
                'module'=>$this->params['module'],
                'controller'=> $this->params['controller']
            ));
        }

        $moduleName = Url::toClass($template['module']);
        $controllerName = Url::toClass($template['controller']);
        $actionName = Url::toClass($template['action']);

        $template = LI_APP.'/Modules/'.$moduleName.'/View/'.$controllerName.'/'.$actionName.'.php';

        $this->data = $data;

        ob_start();
        include $template;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Affichage de vues partielles dans la vue
     * @param unknown_type $template
     * @param unknown_type $data
     * @return Ambigous <void, string>
     */
    public function render_partial( $template, $data = array() )
    {
        $template = _selector($template, array(
            'module'=>$this->params['module'],
            'controller'=> $this->params['controller']
        ));

        // Nouveau objet, pour ne pas casser la construction de la vue principale
        //$template['action'] = '_'.$template['action'];
        $partialView = new View($this->controller);

        return $partialView->render($template, $data);

        return $this->render($template, $data);
    }

    /**
     * synonime plus court
     * @param unknown_type $template
     * @param unknown_type $data
     */
    public function partial($template, $data = array())
    {
        return $this->render_partial($template, $data);
    }

    /**
     * Un Getter, mais PAS de Setter (on est dans la vue hein !!)
     * Il sera utilisé dans le fichier de template
     */
    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }
}
