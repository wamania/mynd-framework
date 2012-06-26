<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Project Generator</title>
<?php echo _js('jquery-1.7.2.min.js'); ?>
<?php echo _js('bootstrap.min.js'); ?>
<?php echo _css('bootstrap.min.css'); ?>
</head>
<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav nav-pills">
				<li <?php if ($this->params['controller']=='program'): echo 'class="active"'; endif; ?>><a href="<?php echo _url('default:program:index'); ?>">Nouveau projet</a></li>
				<?php if (!empty($this->program_id)): ?>
				<li <?php if ( ($this->params['controller']=='lot') && ($this->params['action']=='globalList') ): echo 'class="active"'; endif; ?>><a href="<?php echo _url('default:lot:globalList'); ?>">Vue globale</a></li>
				<li <?php if ( ($this->params['controller']=='lot') && ($this->params['action']!='globalList') ): echo 'class="active"'; endif; ?>><a href="<?php echo _url('default:lot:index'); ?>">Lots</a></li>
				<li <?php if ($this->params['controller']=='client'): echo 'class="active"'; endif; ?>><a href="<?php echo _url('default:client:index'); ?>">Clients</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<?php echo $this->layout_content; ?>
</body>
</html>