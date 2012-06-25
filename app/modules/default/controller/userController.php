<?php

class userController extends LiController {
	
	/**
	 * Gestion des  actions / layout
	 * @return 
	 */
	protected $actions_with_layout = array(
		'index' => 'base',
		'register' => 'base'
	);
	
	public function index() {
	
	}
	
	public function register() {
		$this->user = new User($this->params['user']);
		if ( empty($this->params['user']['pseudo'])) {
	    	$this->notice .= 'Veuillez remplir le champ login<br />';
	    }
		if ( empty($this->params['user']['password'])) {
			$this->notice .= 'Veuillez remplir le champ mot de passe<br />';
		}
		if ($this->params['user']['password'] != $this->params['user']['password_confirm']) {
			$this->notice .= 'Le mot de passe et la confirmation sont différents<br />';
		} else {
			unset($this->params['password_confirm']);
		}
		if ( empty($this->params['user']['email'])) {
			$this->notice .= 'Veuillez remplir le champ E-mail<br />';
		}
		if ($this->params['user']['verif'] != $this->session['code']) {
			$this->notice .= 'Le code de l\'image ne correspond pas.<br />';
		}
		
		$user = User::$objects->filter("(pseudo=?) OR (email=?)", array($this->params['user']['pseudo'], sha1($this->params['user']['email'])));
		if ($user->count() > 0) {
			$this->notice .= 'Un compte existe déjà avec ce login ou cet E-mail';
		}
		
		if (empty($this->notice)) {
			$this->user->classe_id = 1;
			$this->user->logged_in_on = SDateTime::now();
			$this->user->password = sha1($this->user->password);
			if ($this->user->save()) {
				$this->notice = 'Vous êtes Enregistré';
			}
		}
	}
	
	public function getSecuImage() {
		$liste = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$code = '';
		
		while(strlen($code) != 5) {
			$code .= $liste[rand(0,63)];
		}
		
		$this->session['code'] = $code;
		
		$larg = 50;
		$haut =20;
		$img = imageCreate($larg, $haut);
		$rouge = imageColorAllocate($img,255,0,0);
		$noir = imageColorAllocate($img,0,0,0);
		$code_police=5;
		//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header('Cache-Control: post-check=0, pre-check=0', false); 
		header("Content-type: image/jpeg");
		
		imageString($img, $code_police,($larg-imageFontWidth($code_police)*strlen("".$code.""))/2,0, $code,$noir);
		
		imagejpeg($img,'',40);
		imageDestroy($img);
	}
}
?>
