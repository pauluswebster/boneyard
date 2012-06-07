<?php

namespace centrifuge\models;

class Projects extends \lithium\data\Model {
	
	protected $_meta = array(
		'connection' => 'centrifuge'
	);
	
	public $validates = array(
		'title' => 'please enter a title'
	);
}

?>