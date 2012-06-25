<?php

class Category extends LiModel {
    
	/**
	 * Tableau contenant les relations entre classes
	 */
	protected $associations = array(
		'forum' => array(
			'type' => 'hasmany',
			'class' => 'Forum'
		)
	);
}
?>