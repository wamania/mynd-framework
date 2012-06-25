<?php

class Forum extends LiModel {
    
	/**
	 * Tableau contenant les relations entre classes
	 */
	protected $associations = array(
		'category' => array(
			'type' => 'belongsto',
			'class' => 'Category'
		),
        'moderator' => array(
            'type' => 'manytomany',
            'class' => 'Moderator'
        )
	);
}