<?php
/**
 * Fichier hasone.php
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

require_once LI_MODEL.'test/models/hasoneperso.php';
require_once LI_MODEL.'test/models/hasoneuser.php';

class TestHasOne extends framyORMTest 
{
    protected $sql = 'hasone.sql';
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function setUp() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE perso");
        $db->query("TRUNCATE user");
    }
    
    public function tearDown() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE perso");
        $db->query("TRUNCATE user");
    }
    
    public function TestCreate() 
    {
        $user = new HasOneUser();
        $user->login = 'hasone';
        $user->email = 'hasone@framy.com';
        $user->password = 'secret';
        $user->perso->set(new HasOnePerso(array('name' => 'James Bond')));
        $user->save();

        $user1 = HasOneUser::get('login=?', array('hasone'));

        $this->assertEqual($user1->perso->count(), 1);
        $perso = $user1->perso->get();
        
        // check login
        $this->assertEqual($perso->name, 'James Bond');
        
        // cast id from string to int
        $perso_id = intval($perso->id);
        
        // check if cast don't change value
        $this->assertEqual($perso_id, $perso->id);

        // Check if id is not null
        $this->assertNotNull($perso_id);
        
        // check if id is not zero
        $this->assertNotEqual($perso_id, 0);
    }
    
    public function TestCreateFromOneObjectByConstructor() 
    {
        // we add just ONE car
        $user = new HasOneUser(array(
            'perso' => new HasOnePerso(array('name' => 'James Bond')),
            'login' => 'perso by constructor 2',
            'email' => 'persoyconstructor@framy.com'
        ));
        $user->save();
        
        $user1 = HasOneUser::get('login=?', array('perso by constructor 2'));
        $this->assertEqual($user1->perso->count(), 1);
        $perso = $user1->perso->get();
        
        // check login
        $this->assertEqual($perso->name, 'James Bond');
        
        // cast id from string to int
        $perso_id = intval($perso->id);
        
        // check if cast don't change value
        $this->assertEqual($perso_id, $perso->id);

        // Check if id is not null
        $this->assertNotNull($perso_id);
        
        // check if id is not zero
        $this->assertNotEqual($perso_id, 0);
    }
    
    public function TestRead() 
    {
        // we add an array of car
        $user = new HasOneUser(array(
            'perso' => new HasOnePerso(array('name' => 'James Bond')),
            'login' => 'user2',
            'email' => 'user2@framy.com'
        ));
        $user->save();
        
        $user = HasOneUser::get('login=?', array('user2'));
        
        $perso = $user->perso->get();
        $this->assertIsA($perso, 'HasOnePerso');
        $this->assertNotNull($perso->name);
        $this->assertNotNull($perso->id);
    }
    
    public function TestReadJoin() 
    {
        $user = new HasOneUser(array(
            'perso' => new HasOnePerso(array('name' => 'James Bond')),
            'login' => 'user2',
            'email' => 'user2@framy.com'
        ));
        $user->save();
        
        $users = HasOneUser::find()->join('perso')->where('user.login=?', 'user2');
        
        $this->assertEqual($users->count(), 1);
        
        $user1 = $users->current();
        
        $this->assertEqual($user1->perso->count(), 1);
        
        $perso = $user1->perso->get();
        $this->assertEqual($perso->name, 'James Bond');
        
        $user1->perso->next();
        $perso2 = $user1->perso->current();
        $this->assertNull($perso2);
        
        $user->perso->delete();
        $user->save();
        
        $this->assertEqual($user->perso->count(), 0);
        
        $perso3 = new HasOnePerso(array(
            'name' => 'Dr No',
            'user' => $user
        ));
        $perso3->save();
        
        $user2 = HasOneUser::get('login=?', 'user2');
        $this->assertEqual($user2->perso->count(), 1);
        $this->assertEqual($user2->perso->name, 'Dr No');
    }
    
    public function TestUpdate() 
    {
        $user = new HasOneUser(array(
            'perso' => new HasOnePerso(array('name' => 'James Bond')),
            'login' => 'user2',
            'email' => 'user2@framy.com'
        ));
        $user->save();
        
        $user1 = HasOneUser::get('login=?', 'user2');
        
        $perso = $user1->perso->get();
        $perso->name = 'updated perso name';
        $perso->save();
        
        $newpersos = HasOnePerso::find()->where('name=?', 'updated perso name');
        $this->assertEqual($newpersos->count(), 1);
        
        $this->assertEqual($newpersos->current()->name, 'updated perso name');
    }
    
    
    public function TestDelete() 
    {
        // we add an array of car
        $user = new HasOneUser(array(
            'perso' => new HasOnePerso(array('name' => 'James Bond')),
            'login' => 'user2',
            'email' => 'user2@framy.com'
        ));
        $user->save();
        
        $user->perso->delete();
        $user->save();
        
        $perso = HasOnePerso::get('name=?', 'James Bond');
        $this->assertNull($perso);
    }
}
