<?php
class HasOneUser extends LiModel {
    
    public $table = 'user';
    
    protected $associations = array(
        'perso' => array(
            'type' => 'hasone',
            'class' => 'HasOnePerso'
        )
    );
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
