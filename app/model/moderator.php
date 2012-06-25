<?php

class Moderator extends LiModel {
    
	/**
	 * Tableau contenant les relations entre classes
	 */
	protected $associations = array(
		'forum' => array(
			'type' => 'manytomany',
			'class' => 'Forum'
		),
        'user' => array(
            'type' => 'belongsto',
            'class' => 'User'
        )
	);
}