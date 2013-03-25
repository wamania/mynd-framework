<?php
/**
 * Fichier test/model.php
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


require_once './utils/MfTestModel.php';
require_once './models/user.php';

//require_once LI_MODEL.'test/models/simplecruduser.php';

class TestModel extends MfTestModel
{
    protected $sql = 'model.sql';

    public function setUp()
    {
        $this->db->query("TRUNCATE user");
    }

    public function tearDown()
    {
        $this->db->query("TRUNCATE user");
    }

    public function testCreate()
    {
        $user = new User();
        $user->login = 'Framy';
        $user->email = 'framy@framy.com';
        $user->password = 'secret';
        $user->save();

        $user1 = User::one(1);

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
        $user = new User(array('login' => 'by constructor', 'email' => 'constructor@framy.com', 'password' => 'secret'));
        $user->save();

        $user1 = User::one('login=?', array('by constructor'));

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
        $user = new User();
        $user->login = 'Framy_update';
        $user->email = 'framy@framy.com';
        $user->password = 'secret';
        $user->save();

        // Update user
        $user1 = User::one('login=?', array('Framy_update'));
        $user1->login = 'Framy after update';
        $user1->save();

        // check
        $user2 = User::one('login=?', array('Framy after update'));
        $this->assertNotNull($user2);
        $this->assertEqual($user2->login, $user1->login);
    }

    public function testRead()
    {
        $user = new User(array('login' => 'user1', 'email' => 'user1@framy.com', 'password' => 'secret'));
        $user->save();

        $user2 = new User(array('login' => 'user2', 'email' => 'user2@framy.com', 'password' => 'secret'));
        $user2->save();

        $users = User::select();
        foreach ($users as $user) {
            $this->assertNotNull($user);
            $this->assertIsA($user, 'User');
            $this->assertNotNull($user->login);
            $this->assertNotNull($user->id);
        }

        $user1 = User::one(1);
        $this->assertNotNull($user1);
        $this->assertIsA($user1, 'User');
        $this->assertNotNull($user1->login);
        $this->assertNotNull($user1->id);
    }

    public function testDelete()
    {
        $user = new User(array('login' => 'user1', 'email' => 'user1@framy.com', 'password' => 'secret'));
        $user->save();

        $user = User::one(1);
        $user->delete();

        $user1 = User::one(1);
        $this->assertNull($user1);
    }
}
