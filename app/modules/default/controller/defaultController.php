<?php
class defaultController extends LiController {

	public function index() {
	    
        $db = _r('db');
        $db->query("TRUNCATE category");
        $db->query("TRUNCATE forum");
        $db->query("TRUNCATE forum_moderator");
        $db->query("TRUNCATE moderator");
        $db->query("TRUNCATE post");
        $db->query("TRUNCATE subject");
        $db->query("TRUNCATE user");
        
        // test manytomany
        $user = new User(array('login' => 'user1'));
        $modo = new Moderator(array('forum' => new Forum(array('name' => 'Fofo du modo 1'))));
        $modo->forum->add(new Forum(array('name' => 'le Fofo 2 du modo 1')));
        $modo->forum->add(new Forum(array('name' => 'le Fofo 3 du modo 1')));
        $user->moderator->set($modo);
        $user->save();
        
        $modo->forum->delete(Forum::get('name=?', array('Fofo du modo 1')));
        $modo->save();
        
        // test hasmany
        $cat = new Category(array('name' => 'Cats 1', 'forum' => new Forum(array('name' => 'fofo 0'))));
        $cat->forum->add(new Forum(array('name' => 'fofo 1')));
        $cat->forum->add(new Forum(array('name' => 'fofo2')));
        $cat->save();
        
        // Création catégorie
        $cat = new Category;
        $cat->name = 'Download';
        $cat->cat_order = 1;
        $cat->save();
        
        $cat->forum->add(new Forum(array('name'=>'lastest download')));
        $cat->save();
        
        $forum1 = Forum::get(1);
        
        $user1 = new User(array('login'=>'user1'));
        $moderateur1 = new Moderator();
        $moderateur1->forum->add(
            new Forum(array(
                'name' => 'fofo de user1', 
                'category' => new Category(array(
                    'name' => "User's forums",
                    'cat_order' => 1
                ))
            ))
        );
        $user1->moderator->set($moderateur1);
        $user1->save();
        
        $user2 = new User;
        $user2->moderator->set(new Moderator(array('forum' => new Forum(array('name' => 'le fofo du user2')))));
        $user2->login = 'user2';
        $user2->save();
        
        $fofo2 = Forum::get("name=?", array('le fofo du user2'));
        $cat = Category::get("name=?", array("User's forums"));
        $fofo2->category->set(Category::get("name=?", array("User's forums")));
        $fofo2->save();
        
        $user3 = new User();
        $user3->login = 'user3';
        $user3->save();
        
		$this->categories = Category::find()->join('forum');
        
        $toto = '';
        
        foreach ($this->categories as $cat) {
            $toto .= 'Cat : '.$cat->name.'<br />';
            foreach ($cat->forum as $fofo) {
                $toto .= '&nbsp;&nbsp;&nbsp; Forum : '.$fofo->name.' ('.$fofo->category->name.')<br />';
                foreach ($fofo->moderator as $modo) {
                    $toto .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; modo : '.$modo->user->login."<br />";
                }
            }
        }
        
        $this->forum = Forum::find()->join('category');
        foreach ($this->forum as $fofo) {
            echo $fofo->name.' ['.$fofo->category->name.']'."\n<br />";
        }
        
        $this->users = User::find()->join('moderator');
        foreach ($this->users as $user) {
            echo $user->login.' ['.($user->moderator->exists() ? ' est modo' : ' est PAS modo]')."\n<br />";
        }
        
        $this->users = User::find();
        foreach ($this->users as $user) {

            if ($user->moderator->exists()) {
                $toto .= $user->login.' is modo'."\n";
            } else {
                $toto .= $user->login.' is not modo'."\n";
            }
        }
       $user1 = User::get(1);
       $forum2 = Forum::get(2);
       $forum2->moderator->add($user1->moderator->get());
       $forum2->save();
       $this->forums = Forum::find()->join('moderator');
       foreach ($this->forums as $fofo) {
           echo 'fofo :'.$fofo->name."\n<br />";
           foreach ($fofo->moderator as $modo) {
               echo '&nbsp; &nbsp; '.$modo->user->login."\n<br />"; 
           }
       }
	}
}
?>