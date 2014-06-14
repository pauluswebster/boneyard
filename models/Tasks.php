<?php

namespace centrifuge\models;

class Tasks extends Centrifuge {
	
	public $validates = array(
		'title' => 'please enter a title'
	);
}

?>