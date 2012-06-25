<?php

class forumController extends LiController {
	
	/**
	 * Gestion des  actions / layout
	 * @return 
	 */
	protected $actions_with_layout = array(
		'default' => 'base'
	);
	
	public function index() {
		$this->categories = Categorie::find()->order_by('cat_order');
	}
	
	public function forum() {
	    if (!isset($this->params['id'])) {
	        $this->redirect_to('forum:forum:index');
	    }
	    $this->forum = Forum::get($this->params['id']);
	}
}
?>
