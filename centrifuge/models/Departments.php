<?php

namespace centrifuge\models;

class Departments extends Centrifuge {
	
	public $validates = array(
		'title' => 'please enter a title'
	);
	
	public static $summaryFields = array('title');
}

?>