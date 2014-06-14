<?php

namespace centrifuge\models;

class Staff extends Centrifuge {
	
	public static $summaryFields = array('first_name', 'last_name');
	
	protected $_meta = array(
		'title' => 'first_name',
	);
}

?>