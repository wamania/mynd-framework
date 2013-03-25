<?php
if (!defined('LI_LIB'))
    define ('LI_LIB', dirname(__FILE__).'/../../../lib/');

require_once LI_LIB.'database/index.php';
require_once LI_LIB.'model/index.php';
require_once LI_LIB.'core/registery.class.php';
require_once LI_LIB.'utils/minihelpers.php';
require_once '../simpletest/autorun.php';

define('DB_DSN', 'mysql:host=localhost;dbname=mftest');
define('DB_USER', 'root');
define('DB_PASS', 'meuhmeuh');

$db = new MfDb(DB_DSN, DB_USER, DB_PASS);
MfRegistery::set('db', $db);

class MfTestModel extends UnitTestCase
{
    protected $db;

    protected $sql;

    public function __construct()
    {
        $this->db = _r('db');

        if (!empty($this->sql)) {
            $content = file_get_contents('sql/'.$this->sql, 'r');
            $tabSql = explode(';', $content);
            foreach ($tabSql as $sql) {
                $sql = trim($sql);
                if (!empty($sql)) {
                    $this->db->query($sql);
                }
            }
        }
    }
}
