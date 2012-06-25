<?php
/**
 * Fichier hasmany.php
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

require_once LI_MODEL.'test/models/hasmanycar.php';
require_once LI_MODEL.'test/models/hasmanyuser.php';

class TestHasMany extends framyORMTest 
{
    protected $sql = 'hasmany.sql';
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function setUp() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE car");
    }
    
    public function tearDown() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE car");
    }
    
    public function TestCreate() 
    {
        $user = new HasManyUser();
        $user->login = 'hasmany';
        $user->email = 'hamany@framy.com';
        $user->password = 'secret';
        $user->car->add(new HasManyCar(array('name' => 'Aston Martin')));
        $user->save();

        $user1 = HasManyUser::get('login=?', array('hasmany'));

        $this->assertEqual($user1->car->count(), 1);
        $car = $user1->car->current();
        
        // check login
        $this->assertEqual($car->name, 'Aston Martin');
        
        // cast id from string to int
        $car_id = intval($car->id);
        
        // check if cast don't change value
        $this->assertEqual($car_id, $car->id);

        // Check if id is not null
        $this->assertNotNull($car_id);
        
        // check if id is not zero
        $this->assertNotEqual($car_id, 0);
    }
    
    public function TestCreateFromArrayByConstructor() 
    {
        // we add an array of car
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'car by constructor',
            'email' => 'carbyconstructor@framy.com'
        ));
        $user->save();
        
        $user1 = HasManyUser::get('login=?', array('car by constructor'));

        $this->assertEqual($user1->car->count(), 2);
        $car = $user1->car->current();
        
        // check login
        $this->assertEqual($car->name, 'Aston Martin');
        
        // cast id from string to int
        $car_id = intval($car->id);
        
        // check if cast don't change value
        $this->assertEqual($car_id, $car->id);

        // Check if id is not null
        $this->assertNotNull($car_id);
        
        // check if id is not zero
        $this->assertNotEqual($car_id, 0);
    }
    
    public function TestCreateFromOneObjectByConstructor() 
    {
        // we add just ONE car
        $user = new HasManyUser(array(
            'car' => new HasManyCar(array('name' => 'Aston Martin')),
            'login' => 'car by constructor 2',
            'email' => 'carbyconstructor@framy.com'
        ));
        $user->save();
        
        $user1 = HasManyUser::get('login=?', array('car by constructor 2'));
        $this->assertEqual($user1->car->count(), 1);
        $car = $user1->car->current();
        
        // check login
        $this->assertEqual($car->name, 'Aston Martin');
        
        // cast id from string to int
        $car_id = intval($car->id);
        
        // check if cast don't change value
        $this->assertEqual($car_id, $car->id);

        // Check if id is not null
        $this->assertNotNull($car_id);
        
        // check if id is not zero
        $this->assertNotEqual($car_id, 0);
    }
    
    public function TestRead() 
    {
        // we add an array of car
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'readcars',
            'email' => 'readcars@framy.com'
        ));
        $user->save();
        
        $user1 = HasManyUser::get('login=?', array('readcars'));
        
        foreach ($user1->car as $car) {
            $this->assertIsA($car, 'HasManyCar');
            $this->assertNotNull($car->name);
            $this->assertNotNull($car->id);
        }
        
        $users = HasManyUser::find()->where('car.name=?', 'Aston Martin');
        $this->assertEqual($users->count(), 1);
        $this->assertIsA($users->current(), 'HasManyUser');
        $this->assertEqual($users->current()->car->current()->name, 'Aston Martin');
        
        $users2 = HasManyUser::find()->where('car.name=? OR car.name=?', array('Aston Martin', 'Ferrari'));
        $this->assertEqual($users2->count(), 1);
        $this->assertIsA($users2->current(), 'HasManyUser');
        $this->assertEqual($users2->current()->car->count(), 2);
        foreach ($users->current()->car as $car) {
            $this->assertIsA($car, 'HasManyCar');
            $this->assertNotNull($car->name);
        }
    }
    
    public function TestReadJoin() 
    {
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'testjoin',
            'email' => 'testjoin@framy.com'
        ));
        $user->save();
        
        $users = HasManyUser::find()->join('car')->where('user.login=?', 'testjoin');
        
        $this->assertEqual($users->count(), 1);
        
        $user1 = $users->current();
        
        $this->assertEqual($user1->car->count(), 2);
        
        $car1 = $user1->car->current();
        $this->assertEqual($car1->name, 'Aston Martin');
        
        $user1->car->next();
        $car2 = $user1->car->current();
        $this->assertEqual($car2->name, 'Ferrari');
        
        $car = new HasManyCar(array(
            'name' => 'la voiture de John',
            'user' => $user1
        ));
        $car->save();
        
        $new_user1 = HasManyUser::get('login=?', 'testjoin');
        $this->assertEqual($new_user1->car->count(), 3);
    }
    
    public function TestUpdate() 
    {
        // we add an array of car
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'updatecars',
            'email' => 'updatecars@framy.com'
        ));
        $user->save();
        
        $user1 = HasManyUser::get('login=?', array('updatecars'));
        
        foreach ($user1->car as $car) {
            $car->name = 'updated car name';
            $car->save();
        }
        
        $newcars = HasManyCar::find()->where('name=?', 'updated car name');
        $this->assertEqual($newcars->count(), 2);
        foreach ($newcars as $car) {
            $this->assertEqual($car->name, 'updated car name');
        }
    }
    
    
    public function TestDelete() 
    {
        // we add an array of car
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'updatecars',
            'email' => 'updatecars@framy.com'
        ));
        $user->save();
        
        $user->car->delete( HasManyCar::get('name=?', 'Ferrari'));
        $user->save();
        
        $car = HasManyCar::get('name=?', 'Ferrari');
        $this->assertNull($car);
        $this->assertEqual($user->car->count(), 1);
        
        $car2 = $user->car->current();
        $this->assertEqual($car2->name, 'Aston Martin');
    }
    
    public function TestDeleteAll() 
    {
        // we add an array of car
        $user = new HasManyUser(array(
            'car' => array(
                new HasManyCar(array('name' => 'Aston Martin')),
                new HasManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'updatecars',
            'email' => 'updatecars@framy.com'
        ));
        $user->save();
        
        $user->car->deleteAll();
        $user->save();
        
        $this->assertEqual($user->car->count(), 0);
        
        $car = $user->car->current();
        $this->assertNull($car);
    }
}
