<?php
class HasManyUser extends LiModel {
    
    public $table = 'user';
    
    protected $associations = array(
        'car' => array(
            'type' => 'hasmany',
            'class' => 'HasManyCar'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
