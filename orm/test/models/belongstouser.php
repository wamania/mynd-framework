<?php
class BelongsToUser extends LiModel {
    
    public $table = 'user';
    
    protected $associations = array(
        'post' => array(
            'type' => 'hasmany',
            'class' => 'BelongsToPost'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
