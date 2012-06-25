<?php
class Simplecruduser extends LiModel {
    
    //public static $table = 'user';
    
    public static function get($id, $params=array()) {
		return parent::get(get_class(), $id, $params);
	}
	public static function find() {
		return parent::find(get_class());
	}
}
