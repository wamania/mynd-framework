<?php

class User extends LiModel {
    
	/**
	 * Tableau contenant les relations entre classes
	 */
	protected $associations = array(
		'moderator' => array(
			'type' => 'hasone',
			'class' => 'Moderator'
		)
	);
}