<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Project Generator</title>
<script src="<?php echo rootPath().'app/www/js/jquery-1.7.2.min.js'; ?>"></script>
<script src="<?php echo rootPath().'app/www/js/bootstrap.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo rootPath().'app/www/css/bootstrap.min.css'; ?>" media="all">
<style>
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.btn:focus, input, a:focus  {
	outline: none;
}
</style>
</head>
<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav nav-pills">
				<li <?php if ($this->params['controller']=='generator'): echo 'class="active"'; endif; ?>><a href="<?php echo _url('generator:default:index'); ?>">Nouveau projet</a></li>
			</ul>
		</div>
	</div>
</div>
<?php echo $this->layout_content; ?>
</body>
</html>