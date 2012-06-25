<?php
/**
 * Fichier belongsto.php
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

require_once LI_MODEL.'test/models/belongstouser.php';
require_once LI_MODEL.'test/models/belongstopost.php';

class TestBelongsTo extends framyORMTest 
{
    protected $sql = 'belongsto.sql';
    
    public function tearDown() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE post");
    }
    
    public function setUp() 
    {
        $db = LiDb::getSingleton();
        $db->query("TRUNCATE user");
        $db->query("TRUNCATE post");
    }
    
    public function testCreate() 
    {
        $post = new BelongsToPost(array(
            'title' => 'post1',
            'content' => 'content 1',
            'user' => new BelongsToUser(array('login' => 'user 1'))
        ));
        
        $post->save();
        
        $post1 = BelongsToPost::get('title=?', 'post1');
        
        $this->assertEqual($post1->user->login, 'user 1');
        
        $post2 = new BelongsToPost();
        $post2->title = 'Title2';
        $post2->content = 'content 2';
        $post2->user->set(BelongsToUser::get('login=?', 'user 1'));
        $post2->save();
        
        $user1 = BelongsToUser::get('login=?', 'user 1');
        
        $this->assertEqual($user1->post->count(), 2);
    }
    
    public function testUpdate() 
    {
        $post = new BelongsToPost(array(
            'title' => 'post1',
            'content' => 'content 1',
            'user' => new BelongsToUser(array('login' => 'user 1'))
        ));
        
        $post->save();
        
        $post->title = 'post updated 1';
        $post->user->set(new BelongsToUser(array('login' => 'User 2')));
        
        $post->save();
        
        $post1 = BelongsToPost::get('title=?', 'post updated 1');
        
        $this->assertNotNull($post1);
        $this->assertIsA($post1, 'BelongsToPost');
        $this->assertEqual($post1->user->login, 'User 2');
        
        $post->user->login = 'User 3';
        
        $post->save();
        
        $user1= BelongsToUser::get('login=?', 'User 3');
        
        $this->assertNotNull($user1);
        $this->assertIsA($user1, 'BelongsToUser');
        $this->assertEqual($user1->login, 'User 3');
    }
    
    public function testRead() 
    {
        $post = new BelongsToPost(array(
            'title' => 'post1',
            'content' => 'content 1',
            'user' => new BelongsToUser(array('login' => 'user 1'))
        ));
        
        $post->save();
        
        $user = $post->user->get();
        
        $this->assertNotNull($user);
        $this->assertIsA($user, 'BelongsToUser');
        $this->assertEqual($user->login, 'user 1');
    }
    
    public function testDelete() 
    {
        $post = new BelongsToPost(array(
            'title' => 'post1',
            'content' => 'content 1',
            'user' => new BelongsToUser(array('login' => 'user 1'))
        ));
        
        $post->save();
        
        $post1 = BelongsToPost::get('title=?','post1');
        $this->assertNotNull($post1->user->get());
        $this->assertEqual($post1->user->login, 'user 1');
        
        $post1->user->delete();
        $post1->content = 'content updated delete';
        $post1->save();
        
        $this->assertNull($post1->user->get());
        $this->assertEqual($post1->content, 'content updated delete');
        $this->assertEqual($post1->user->count(), 0);
    }
}

