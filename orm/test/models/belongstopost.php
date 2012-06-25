<?php
class BelongsToPost extends LiModel {
    
    public $table = 'post';
    
    protected $associations = array(
        'user' => array(
            'type' => 'belongsto',
            'class' => 'BelongsToUser',
            'foreign_key' => 'id',
            'local_key' => 'user_id'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
