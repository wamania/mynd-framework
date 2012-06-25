<?php
class ManyToManyCar extends LiModel {
    
    public $table = 'car';
    
    protected $associations = array(
        'muser' => array(
            'type' => 'manytomany',
            'class' => 'ManyToManyUser'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
