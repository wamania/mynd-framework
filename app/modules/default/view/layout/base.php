<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
* {
	padding:0;
	margin:0;
}
body {
	font-family:Arial;
	font-size:14px;
	/*background:url('<?= _img('bg.jpg'); ?>') repeat-x;*/
}
#all {
	width:1000px;
	margin:10px auto;
}
#header {
	height:40px;
	text-align:center;
	font-size:22px;
}
#loginbox {
	border:solid 1px #00FF00;
	/*background-color:#0abcdf;*/
	height:40px;
	padding-top:12px;
	padding-left:15px;
	text-align:center;
}
#loginbox input {
	margin-right:25px;
	border:solid 1px #0accef;
	background-color:#FFFFFF;
}
#loginbox input[type=text] {
	height:20px;
	font-size:18px;
	color:#689BDE;
}
#loginbox label {
	cursor:pointer;
}
#core {
	border:solid 1px #FF0000;
	margin-top:15px;
	/*background-color:#0abcdf;*/
}
#footer {
	height:25px;
	border:solid 1px #0000FF;
	margin-top:15px;
}
/** Page index */
.cat_block {
	margin:3px;
	margin-bottom:20px;
}
.cat_block > span {
	font-size:16px;
	color:#FF8020;
	font-weight:bold;
	margin-left:10px;
}
.forum_block {
	font-size:14px;
	border:solid 1px #60A0FF;
	background-color:#FFEFD0;
	margin:5px;
	margin-left:30px;
	padding:5px;
}
.forum_block:hover {
	border:solid 1px #5090EF;
	background-color:#EFDFC0;
}
</style>
</head>
<body>
	<div id="all">
		<div id="header">
		Framy Froum
		</div>
		<div id="loginbox">
    		<form action="" method="post">
        		<label for="user[pseudo]">Pseudo : </label>
        		<input type="text" id="pseudo_input" name="user[pseudo]" />
        		<label for="mdp_input">Mot de passe : </label>
        		<input type="text" id="mdp_input" name="user[mdp]" />
        		<input type="submit" value="Se Connecter" />
    		</form>
		</div>
		<div id="core">
		<?= $this->layout_content; ?>
		</div>
		<div id="footer">
		</div>
	</div>
</body>
</html>