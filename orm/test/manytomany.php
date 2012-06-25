<?php
/**
 * Fichier manytomany.php
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

require_once LI_MODEL.'test/models/manytomanycar.php';
require_once LI_MODEL.'test/models/manytomanyuser.php';

class TestManyToMany extends framyORMTest 
{
    protected $sql = 'manytomany.sql';
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function tearDown() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE car");
    }
    
    public function setUp() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE car");
    }
    
    public function TestCreate() 
    {
        $user = new ManyToManyUser();
        $user->login = 'manytomany';
        $user->email = 'manytomany@framy.com';
        $user->password = 'secret';
        $user->car->add(new ManyToManyCar(array('name' => 'Aston Martin')));
        $user->save();
        
        $user1 = ManyToManyUser::get('login=?', array('manytomany'));
        //print_r($user1->car);
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
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'car by constructor',
            'email' => 'carbyconstructor@framy.com'
        ));
        $user->save();
        
        $user1 = ManyToManyUser::get('login=?', array('car by constructor'));

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
        $user = new ManyToManyUser(array(
            'car' => new ManyToManyCar(array('name' => 'Aston Martin')),
            'login' => 'car by constructor 2',
            'email' => 'carbyconstructor@framy.com'
        ));
        $user->save();
        
        $user1 = ManyToManyUser::get('login=?', array('car by constructor 2'));
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
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'readcars',
            'email' => 'readcars@framy.com'
        ));
        $user->save();
        
        $user1 = ManyToManyUser::get('login=?', array('readcars'));
        
        foreach ($user1->car as $car) {
            $this->assertIsA($car, 'ManyToManyCar');
            $this->assertNotNull($car->name);
            $this->assertNotNull($car->id);
        }
    }
    
    public function TestReadJoin() 
    {
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'testjoin',
            'email' => 'testjoin@framy.com'
        ));
        $user->save();
        
        $users = ManyToManyUser::find()->join('car')->where('login=?', 'testjoin');
        
        $this->assertEqual($users->count(), 1);
        
        $user1 = $users->current();
        
        $this->assertEqual($user1->car->count(), 2);
        
        $car1 = $user1->car->current();
        $this->assertEqual($car1->name, 'Aston Martin');
        
        $user1->car->next();
        $car2 = $user1->car->current();
        $this->assertEqual($car2->name, 'Ferrari');
        
        $car = new ManyToManyCar(array(
            'name' => 'la voiture de John',
            'muser' => $user1
        ));
        $car->save();
        
        $new_user1 = ManyToManyUser::get('login=?', 'testjoin');
        $this->assertEqual($new_user1->car->count(), 3);
    }
    
    public function TestUpdate() 
    {
        // we add an array of car
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'updatecars',
            'email' => 'updatecars@framy.com'
        ));
        $user->save();
        
        $user1 = ManyToManyUser::get('login=?', array('updatecars'));
        
        foreach ($user1->car as $car) {
            $car->name = 'updated car name';
            $car->save();
        }
        
        $newcars = ManyToManyCar::find()->where('name=?', 'updated car name');
        $this->assertEqual($newcars->count(), 2);
        foreach ($newcars as $car) {
            $this->assertEqual($car->name, 'updated car name');
        }
    }
    
    
    public function TestDelete() 
    {
        // we add an array of car
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
            ),
            'login' => 'updatecars',
            'email' => 'updatecars@framy.com'
        ));
        $user->save();
        
        $user->car->delete( ManyToManyCar::get('name=?', 'Ferrari'));
        $user->save();
        
        $car = ManyToManyCar::get('name=?', 'Ferrari');
        $this->assertNotNull($car);
        $this->assertIsA($car, 'ManyToManyCar');
        $this->assertEqual($user->car->count(), 1);
        
        $car2 = $user->car->current();
        $this->assertEqual($car2->name, 'Aston Martin');
    }
    
    public function TestDeleteAll() 
    {
        // we add an array of car
        $user = new ManyToManyUser(array(
            'car' => array(
                new ManyToManyCar(array('name' => 'Aston Martin')),
                new ManyToManyCar(array('name' => 'Ferrari'))
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
