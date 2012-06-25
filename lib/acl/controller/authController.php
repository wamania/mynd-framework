<?php

class LiAuthController extends LiController {
	
	public function register() {
		
		/*$this->user = new User($this->params['user']);
		if ( empty($this->params['login'])) {
	    	$this->notice .= 'Veuillez remplir le champ login<br />';
	    }
		if ( empty($this->params['mdp'])) {
			$this->notice .= 'Veuillez remplir le champ mot de passe<br />';
		}
		if ($this->params['mdp'] != $this->params['mdp_confirm']) {
			$this->notice .= 'Le mot de passe et la confirmation sont différents<br />';
		}
		if ( empty($this->params['email'])) {
			$this->notice .= 'Veuillez remplir le champ E-mail<br />';
		}
		
		$user = User::$objects->filter("(login=?) OR (email=?)", array($this->params['login'], sha1($this->params['email'])));
		if ($user->count() > 0) {
			$this->notice .= 'Un compte existe déjà avec ce login ou cet E-mail';
		}
		
		if (empty($this->notice)) {
			$this->user->mdp = sha1($this->user->mdp);
			if ($this->user->save()) {
				$this->notice = 'Vous êtes Enregistré';
			}
		}*/
	}
	
	public function getpass() {
		
	}
	
	public function login() {
		
		$acl_config = _c('acl_config');
		$pseudo_field = $acl_config['pseudo_field'];
		$password_field = $acl_config['password_field'];
		
		if (empty($this->params['user'][$pseudo_field])) {
			$this->session['error'] = 'Champs pseudo vide';
			$this->redirect_to($this->session['history']['last']);
		}
		if (empty($this->params['user'][$password_field])) {
			$this->session['error'] = 'Champs mot de passe vide';
			$this->redirect_to($this->session['history']['last']);
		}
		
		// On crée un objet user
		try {
			$this->user = User::$objects->get("($pseudo_field=?) AND ($password_field=?)", array(
				$this->params['user'][$pseudo_field], 
				md5($this->params['user'][$password_field])));
			
			$this->user->logged_in_on = SDateTime::now();
			$this->user->save();
			
			$this->session['user_id'] = $this->user->id;
		
		} catch (SRecordNotFound $e) {
			$this->session['error'] = 'Utilisateur inexistant';
			$this->session['user_id'] = null;
		}
		
		return true;
	}
	
	public function logout() {
		$this->session['user_id'] = null;
		$this->user = null;
		$this->redirect_to($this->session['history']['last']);
	}
}
?>
