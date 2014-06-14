<?php

namespace centrifuge\models;

class Comments extends Centrifuge {
	
	public $validates = array(
		'title' => 'please enter a title'
	);
}

?>