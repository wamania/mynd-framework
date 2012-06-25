<?php
class ManyToManyUser extends LiModel {
    
    public $table = 'user';
    
    protected $associations = array(
        'car' => array(
            'type' => 'manytomany',
            'class' => 'ManyToManyCar'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
