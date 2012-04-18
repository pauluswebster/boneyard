<?php

namespace app\models;

class SportsCars extends Cars {
	
	public $belongsTo = array(
		'Cars' => array(
			'key' => 'id'
		)
	);
	
}