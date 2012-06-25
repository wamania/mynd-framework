<div id="inscription">
	<form name="membre" method="post" action="<?= _action('site:user:register'); ?>">
	<div class="title">Inscription</div>
	<? if (!empty($this->notice)) : ?>
		<div style="margin-right:auto;margin-left:auto;margin-top:6px;margin-bottom:6px;font-weight:bold;width:90%;border:solid 1px red;color:red;">
	<?= $this->notice; ?>
	</div>
	<? endif; ?>
	<div class="text">			
			<p><label>Login :</label>
			<input type="text" name="user[pseudo]" /></p>
			
			<p><label>Mot de passe :</label>
			<input type="password" name="user[password]" /></p>
			
			<p><label>Confirmez le mot de passe :</label>
			<input type="password" name="user[password_confirm]" /></p>
			
			<p><label>Adresse e-mail :</label>
			<input type="text" name="user[email]" /></p>
			
			<p><label>Localisation :</label>
			<input type="text" name="user[localisation]" /></p>
			
			<p><label>Merci de recopier le code suivant <img src="<?= _u('site:user:getSecuImage'); ?>" alt="image de securisation du formulaire" title="image de securisation du formulaire" /></label>
			<input type="text" name="user[verif]" size="10" maxlength="5"  /></p>
			
			<p><label>&nbsp;</label><input type="submit" value="Enregistrer" /></p>
		</div>
		</form>
</div>