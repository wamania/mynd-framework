<?php
/**
 * Notre classe Vue
 */
class MfView {

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
	private $notifier;
	
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
		$this->notifier = &$controller->getNotifier();
	}

	/**
	 * Setter des params provenant de la requete
	 * Permet de les modifier avant affichage
	 * @param $params
	 * @return unknown_type
	 */
	/*public function setParams($params)
	{
		$this->params = $params;
	}*/

	/*public function setNotifier($notifier)
	{
		$this->notifier = $notifier;
	}*/
	
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
	public function render( $template, $data = array() ) {
		
		if ( ! is_array($template)) {
			$template = _selector($template, array(
				'module'=>$this->params['module'], 
				'controller'=> $this->params['controller']
			));
		}
		
		$template = LI_APP.'modules/'.$template['module'].'/view/'.$template['controller'].'/'.$template['action'].'.php';
		
		$this->data = $data;

		ob_start();
		include $template;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function render_partial( $template, $data = array() ) {

		$template = _selector($template, array(
			'module'=>$this->params['module'], 
			'controller'=> $this->params['controller']
		));
		$template['action'] = '_'.$template['action'];

		//$template = LI_APP.'modules/'.$template['module'].'/view/'.$template['controller'].'/_'.$template['action'].'.php';

		$partialView = new MfView($this->controller);

		return $partialView->render($template, $data);
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
?>