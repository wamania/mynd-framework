<?php
class HasManyCar extends LiModel {
    
    public $table = 'car';
    
    protected $associations = array(
        'user' => array(
            'type' => 'belongsto',
            'class' => 'HasManyUser'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
