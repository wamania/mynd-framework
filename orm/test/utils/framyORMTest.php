<?php
require_once LI_MODEL.'test/simpletest/autorun.php';
require_once LI_MODEL.'index.php';

$config_db = array (
    'dsn' => 'mysql:host=localhost;dbname=framy_model',
    'user' => 'root',
    'pass' => 'meuhmeuh'
);

LiInitModel::init(null, $config_db);

class MfTest extends UnitTestCase {

    public function __construct() {

        if (!empty($this->sql)) {

            $db = LiDb::getSingleton();
            $content = file_get_contents(LI_MODEL.'test/sql/'.$this->sql, 'r');
            $tabSql = explode(';', $content);
            foreach ($tabSql as $sql) {
                $sql = trim($sql);
                if (!empty($sql)) {
                    $db->query($sql);
                }
            }
        }
    }
}
