<div class="well container">
    <?php if (!empty($this->errors)): ?>
    <div class="alert alert-error"><?php echo implode("<br>", $this->errors); ?></div>
    <?php endif; ?>
    <form action="<?php echo _url('generator:default:index'); ?>" method="post" class="form-horizontal">
        <fieldset>
		<div class="control-group">
			<label class="control-label" for="name-field">Nom du projet</label>
			<div class="controls">
			<input type="text" name="name" class="span6 " id="name-field" value="<?php if (!empty($this->params['name'])): echo $this->params['name']; endif; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name-field">Environment</label>
			<div class="controls">
			    <label class="checkbox"><input name="environment[]" type="checkbox" value="local" <?php if ( (!isset($this->params['environment'])) || (in_array('local', $this->params['environment'])) ): echo 'checked'; endif; ?>>local</label>
			    <label class="checkbox"><input name="environment[]" type="checkbox" value="development" <?php if ( (!isset($this->params['environment'])) || (in_array('development', $this->params['environment'])) ): echo 'checked'; endif; ?>>Dévelopement</label>
			    <label class="checkbox"><input name="environment[]" type="checkbox" value="production" <?php if ( (!isset($this->params['environment'])) || (in_array('production', $this->params['environment'])) ): echo 'checked'; endif; ?>>Production</label>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name-field">Url Handler</label>
			<div class="controls">
			    <label class="radio" title="index.php?module=m&controller=c&action=a&var=4"><input name="url_handler" type="radio" value="simple" <?php if ( (isset($this->params['url_handler'])) && ($this->params['url_handler'] == 'simple') ): echo 'checked'; endif; ?>>Simple</label>
			    <label class="radio" title="index.php?ps=m/c/a/4"><input name="url_handler" type="radio" value="querystring" <?php if ( (isset($this->params['url_handler'])) && ($this->params['url_handler'] == 'querystring') ): echo 'checked'; endif; ?>>Query String</label>
			    <label class="radio" title="index/m/c/a/4"><input name="url_handler" type="radio" value="multiviews" <?php if ( (isset($this->params['url_handler'])) && ($this->params['url_handler'] == 'multiviews') ): echo 'checked'; endif; ?>>Multiviews</label>
			    <label class="radio" title="/m/c/a/4"><input name="url_handler" type="radio" value="modrewrite" <?php if ( (!isset($this->params['url_handler'])) || ($this->params['url_handler'] == 'modrewrite') ): echo 'checked'; endif; ?>>Mod Rewrite</label>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Créer le projet</button>
		</div>
		</fieldset>
    </form>
</div>