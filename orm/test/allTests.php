<?php
/**
 * Fichier allTests.php
 * Regroupe tous les fichiers tests
 *
 * LICENCE
 *     You are free:
 *       to Share - to copy, distribute and transmit the work
 *       to Remix - to adapt the work
 *     Under the following conditions:
 *       Attribution - You must attribute the work in the manner specified by the author or licensor 
 *       (but not in any way that suggests that they endorse you or your use of the work).
 *       Just keep this header.
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @version    $Id:$
 * @link       http://www.wamania.com
 * @since      File available since Release 0.1
*/

if (!defined('LI_MODEL'))
    define ('LI_MODEL', dirname(__FILE__).'/../');
require_once LI_MODEL.'test/utils/framyORMTest.php';

class AllTests extends TestSuite 
{
    
    function AllTests() 
    {
        $this->TestSuite('All tests');
        $this->addFile(LI_MODEL.'test/simplecrud.php');
        $this->addFile(LI_MODEL.'test/manytomany.php');
        $this->addFile(LI_MODEL.'test/belongsto.php');
        $this->addFile(LI_MODEL.'test/hasmany.php');
        $this->addFile(LI_MODEL.'test/hasone.php');
        
    }
}
