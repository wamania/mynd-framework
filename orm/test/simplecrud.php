<?php
/**
 * Fichier simplecrud.php
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

require_once LI_MODEL.'test/models/simplecruduser.php';

class TestSimpleCrud extends framyORMTest 
{
    protected $sql = 'simplecrud.sql';
    
    public function tearDown() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE simplecruduser");
    }
    
    public function setUp() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE simplecruduser");
    }
    
    public function testCreate() 
    {
        $user = new Simplecruduser();
        $user->login = 'Framy';
        $user->email = 'framy@framy.com';
        $user->password = 'secret';
        $user->save();
        
        $user1 = Simplecruduser::get(1);

        // check login
        $this->assertEqual($user->login, $user1->login);
        
        // check email
        $this->assertEqual($user->email, $user1->email);
        
        // check password
        $this->assertEqual($user->password, $user1->password);
        
        // cast id from string to int
        $user_id = intval($user1->id);
        
        // check if cast don't change value
        $this->assertEqual($user1->id, $user_id);

        // Check if id is not null
        $this->assertNotNull($user_id);
        
        // check if id is not zero
        $this->assertNotEqual($user_id, 0);
    }
    
    public function testCreateByConstructeur() 
    {
        $user = new Simplecruduser(array('login' => 'by constructor', 'email' => 'constructor@framy.com', 'password' => 'secret'));
        $user->save();
        
        $user1 = Simplecruduser::get('login=?', array('by constructor'));
        
        // check login
        $this->assertEqual($user->login, $user1->login);
        
        // check email
        $this->assertEqual($user->email, $user1->email);
        
        // check password
        $this->assertEqual($user->password, $user1->password);
        
        // cast id from string to int
        $user_id = intval($user1->id);
        
        // check if cast don't change value
        $this->assertEqual($user1->id, $user_id);

        // Check if id is not null
        $this->assertNotNull($user_id);
        
        // check if id is not zero
        $this->assertNotEqual($user_id, 0);
    }
    
    public function testUpdate() 
    {
        // create user
        $user = new Simplecruduser();
        $user->login = 'Framy_update';
        $user->email = 'framy@framy.com';
        $user->password = 'secret';
        $user->save();
        
        // Update user
        $user1 = Simplecruduser::get('login=?', array('Framy_update'));
        $user1->login = 'Framy after update';
        $user1->save();
        
        // check
        $user2 = Simplecruduser::get('login=?', array('Framy after update'));
        $this->assertNotNull($user2);
        $this->assertEqual($user2->login, $user1->login);
    }
    
    public function testRead() 
    {
        $user = new Simplecruduser(array('login' => 'user1', 'email' => 'user1@framy.com', 'password' => 'secret'));
        $user->save();
        
        $user2 = new Simplecruduser(array('login' => 'user2', 'email' => 'user2@framy.com', 'password' => 'secret'));
        $user2->save();
        
        $users = Simplecruduser::find();
        foreach ($users as $user) {
            $this->assertNotNull($user);
            $this->assertIsA($user, 'SimpleCrudUser');
            $this->assertNotNull($user->login);
            $this->assertNotNull($user->id);
        }
        
        $user1 = Simplecruduser::get(1);
        $this->assertNotNull($user1);
        $this->assertIsA($user1, 'SimpleCrudUser');
        $this->assertNotNull($user1->login);
        $this->assertNotNull($user1->id);
    }
    
    public function testDelete() 
    {
        $user = new Simplecruduser(array('login' => 'user1', 'email' => 'user1@framy.com', 'password' => 'secret'));
        $user->save();
        
        $user = Simplecruduser::get(1);
        $user->delete();
        $user->save();
        
        $user1 = Simplecruduser::get(1);
        $this->assertNull($user1);
    }
    
    public function testLimit() 
    {
        for ($i=0; $i<10; $i++) {
            $user = new Simplecruduser(array('login' => 'user'.$i, 'email' => 'user'.$i.'@lithium.com', 'password' => 'secret'));
            $user->save();
        }
        
        $users = Simplecruduser::find()->limit(5);
        $this->assertEqual($users->count(), 5);
        
        $i = 0;
        foreach ($users as $user) {
            $this->assertEqual($user->login, 'user'.$i);
            $i++;
        }
        
        $users = Simplecruduser::find()->limit(5, 3);
        $this->assertEqual($users->count(), 5);
        
        $i = 3;
        foreach ($users as $user) {
            $this->assertEqual($user->login, 'user'.$i);
            $i++;
        }
    }
    
    public function testOrderBy() 
    {
        for ($i=0; $i<10; $i++) {
            $user = new Simplecruduser(array('login' => 'user'.$i, 'email' => 'user'.$i.'@lithium.com', 'password' => 'secret'));
            $user->save();
        }
        
        $users = Simplecruduser::find()->orderBy('-login');
        $this->assertEqual($users->current()->login, 'user9');
    }
}
