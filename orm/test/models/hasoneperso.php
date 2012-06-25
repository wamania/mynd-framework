<?php
class HasOnePerso extends LiModel {
    
    public $table = 'perso';
    
    protected $associations = array(
        'user' => array(
            'type' => 'belongsto',
            'class' => 'HasOneUser'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
