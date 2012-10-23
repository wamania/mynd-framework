<?php

class defaultController extends mfController
{
    /**
     * Gestion des  actions / layout
     * @return
     */
    protected $actions_with_layout = array(
        'index' => 'base'
    );

    public function init()
    {
        if (_r('environment') != 'local') {
            die('FORBIDDEN !');
        }
    }

    public function index()
    {
        if (! defined('MF_WORKSPACE')) {
            die('Vous devez définir le workspace dans le fichier index.php');
        }

        if ($this->request->is_post()){
            $errors = array();

            if (empty($this->params['name'])) {
                $errors[] = "Le nom du projet est vide !";
            } else {
                $workspace = rtrim(MF_WORKSPACE, DIRECTORY_SEPARATOR);
                if (is_dir(MF_WORKSPACE . DIRECTORY_SEPARATOR . $this->params['name'])) {
                    $errors[] = "Le projet existe déjà !";
                }
                if (!is_writable(MF_WORKSPACE . DIRECTORY_SEPARATOR)) {
                    $errors[] = "Le workspace doit être accessible en écriture !";
                }
            }
            if (empty($this->params['environment'])) {
                $errors[] = "Il faut au moins 1 environement !";
            }
            if (empty($this->params['url_handler'])) {
                $errors[] = "Indiquez l'Url Handler !";
            }

            if (empty($errors)) {
                // let's go
                $this->buildFolders();
                $this->buildFiles();
            }

            if (!empty($errors)) {
                $this->errors = $errors;
            }
        }
    }
    private function buildFolders()
    {
        umask(0);
        $workspace = rtrim(MF_WORKSPACE, DIRECTORY_SEPARATOR);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'config', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'helper', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'model', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'controller', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'view' .
            DIRECTORY_SEPARATOR . 'default', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'view' .
            DIRECTORY_SEPARATOR . 'layout', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'css', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'js', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'images', 0777, true);
        mkdir($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'files', 0777, true);
    }

    private function buildFiles()
    {
        $workspace = rtrim(MF_WORKSPACE, DIRECTORY_SEPARATOR);

        // fichier index.php
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'index.php', "<?php
setlocale(LC_ALL, 'fr_FR.utf8');
setlocale(LC_NUMERIC, 'en_US.utf8');
date_default_timezone_set('Europe/Paris');

define ('LI_ROOT', dirname(__FILE__).'/');
define ('LI_APP',  LI_ROOT.'/app/');
define ('LI_LIB',  '".LI_ROOT."lib/');

// Librairie du framework
require_once LI_LIB.'index.php';

// Lancement
MfBoot::init();");

        // fichier app/config/config.php
        $config = '<?php
$config = array
(';
        foreach ($this->params['environment'] as $e) {
            $config .= "\n    '".$e."' => array
    (
        'url_handler' => '".$this->params['url_handler']."',
    ),\n";
        }
        $config .= ');
return $config[_r("environment")];';

        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'config' .
            DIRECTORY_SEPARATOR . 'config.php', $config);

        // fichier app/config/database.php
        $database = '<?php
$database = array
(';
        foreach ($this->params['environment'] as $e) {
            $database .= "\n    '".$e."' => array
    (
        'dsn' => 'mysql:host=localhost;dbname=mf',
        'user' => 'mf',
        'pass' => 'mfsecret'
    ),\n";
        }
        $database .= ');
return $database[_r("environment")];';

        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'config' .
            DIRECTORY_SEPARATOR . 'database.php', $database);

        // fichier app/config/boot.php
        $boot = '<?php

/**
 * Fonction onBoot, lancée automatiquement par le framework juste après la résolution
 * de l\'url, mais avant d\'appeler le couple controller/action
 * @param $request
 * @param $config
 * @return void
 */
function onBoot(&$request, &$config)
{
	$params = &$request->getParams();
}';
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'config' .
            DIRECTORY_SEPARATOR . 'boot.php', $boot);

        // Fichier routes.php
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'config' .
            DIRECTORY_SEPARATOR . 'routes.php', '<?php
$routes = array('."
    // Routes par defaut
    array(
        'url' => '/:module/:controller/:action/:id',
        'params' => array(
            'module'	=> '[a-zA-Z0-9\-]+',
            'controller'=> '[a-zA-Z0-9\-]+',
            'action'	=> '[a-zA-Z0-9\-]+',
            'id' 		=> '[0-9]+'
        )
    ),

    array(
        'url' => '/:module/:controller/:action',
        'params' => array(
            'module'	=> '[a-zA-Z0-9\-]+',
            'controller'=> '[a-zA-Z0-9\-]+',
            'action'	=> '[a-zA-Z0-9\-]+'
        )
    ),

    array(
        'url' => '/:controller/:action',
        'params' => array(
            'module' 	=> _c('default_module'),
            'controller'=> '[a-zA-Z0-9\-]+',
            'action'	=> '[a-zA-Z0-9\-]+',
        )
    ),

    array(
        'url' => '/:module',
        'params' => array(
            'module' 	=> '[a-zA-Z0-9\-]+',
            'controller'=> _c('default_controller'),
            'action'	=> _c('default_action'),
        )
    ),

    array(
        'url' => '/',
        'params' => array(
            'module' 	=> _c('default_module'),
            'controller'=> _c('default_controller'),
            'action'	=> _c('default_action')
        ),
    ),
);".'

return $routes;');

        // fichier app/helper/helper.php
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'helper' .
            DIRECTORY_SEPARATOR . 'helper.php', "<?php\n");

        // fichier app/modules/default/controller/defaultController.php
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'controller' .
            DIRECTORY_SEPARATOR . 'defaultController.php', '<?php

class defaultController extends mfController
{
    protected $filter = array(
		\'before\' => array(

		),
        \'after\' => array(

        )
	);

    protected $actions_with_layout = array(
        \'index\' => \'base\'
    );

    public function init()
    {

    }

    public function index()
    {

    }

    public function finish()
    {

    }
}');
        // fichier app/modules/default/view/layout/base.php
        $base = '<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title></title>
  <?php echo _js(\'jquery-1.7.2.min.js\'); ?>
  <?php echo _js(\'bootstrap.min.js\'); ?>
  <?php echo _css(\'bootstrap.min.css\'); ?>
</head>
<body>
<?php echo $this->layout_content; ?>
</body>
</html>';
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'view' .
            DIRECTORY_SEPARATOR . 'layout' .
            DIRECTORY_SEPARATOR . 'base.php', $base);

        // fichier app/modules/default/view/default/index.php
        $html = '<div class="container" style="margin-top:20px;">
    <div class="hero-unit">
        <h1>Félicitation !</h1>
        <p>Vous venez de créer ce projet avec Mynd Framework. Si vous voyez cette page, c\'est que tout fonctionne.</p>
        <p><a href="https://code.google.com/p/mynd-framework/wiki" class="btn btn-primary btn-large">Documentation</a></p>
    </div>
</div>';
        file_put_contents($workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'app' .
            DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'view' .
            DIRECTORY_SEPARATOR . 'default' .
            DIRECTORY_SEPARATOR . 'index.php', $html);

        // htaccess
        if ($this->params['url_handler'] == 'modrewrite') {
            file_put_contents($workspace .
                DIRECTORY_SEPARATOR . $this->params['name'] .
                DIRECTORY_SEPARATOR . '.htaccess', '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /'.$this->params['name'].'
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?pathinfo=$1 [L,QSA]
</IfModule>');
        }

        copy(LI_ROOT . 'app' .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'js' .
            DIRECTORY_SEPARATOR . 'jquery-1.7.2.min.js',
            $workspace .
            DIRECTORY_SEPARATOR . $this->params['name'] .
            DIRECTORY_SEPARATOR . 'www' .
            DIRECTORY_SEPARATOR . 'js' .
            DIRECTORY_SEPARATOR . 'jquery-1.7.2.min.js');

        copy(LI_ROOT. 'app' .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'js' .
                DIRECTORY_SEPARATOR . 'bootstrap.min.js',
                $workspace .
                DIRECTORY_SEPARATOR . $this->params['name'] .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'js' .
                DIRECTORY_SEPARATOR . 'bootstrap.min.js');

        copy(LI_ROOT. 'app' .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'bootstrap.min.css',
                $workspace .
                DIRECTORY_SEPARATOR . $this->params['name'] .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'bootstrap.min.css');

        copy(LI_ROOT . 'app' .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'glyphicons-halflings.png',
                $workspace .
                DIRECTORY_SEPARATOR . $this->params['name'] .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'glyphicons-halflings.png');

        copy(LI_ROOT . 'app' .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'glyphicons-halflings.png',
                $workspace .
                DIRECTORY_SEPARATOR . $this->params['name'] .
                DIRECTORY_SEPARATOR . 'www' .
                DIRECTORY_SEPARATOR . 'images' .
                DIRECTORY_SEPARATOR . 'glyphicons-halflings-white.png');


    }
}