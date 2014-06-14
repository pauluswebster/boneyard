<?php

namespace app\models;

class Cars extends Listings {
	
	public $belongsTo = array(
		'Listings' => array(
			'key' => 'id'
		)
	);
	
	protected static $_transmissions = array(
		1 => array('Automatic', 'Auto'),
		2 => 'Manual',
		3 => 'Tiptronic',
		8 => 'Other'
	);
	
	protected static $_bodies = array(
		1 => 'Sedan',
		2 => 'Station Wagon',
		3 => 'Hatchback',
		4 => 'Coupe',
		5 => 'Convertable',
		6 => 'RV/SUV',
		7 => 'Ute',
		8 => 'Van',
		9 => 'Truck',
		15 => 'Other'
	);
	
	
	public static function transmissionTypes($short = false) {
		return array_map(function($text) use ($short) {
			$value = (array) $text;
			return $short && isset($value[1]) ? $value[1] : $value[0];
		}, static::$_transmissions);
	}
	
	public static function bodyTypes() {
		return static::$_bodies;
	}
	
	public static function getScaffoldFormFields() {
		$mapped = parent::getScaffoldFormFields();
		$transmissions = static::transmissionTypes(true);
		$mapped['transmission'] = array(
			'list' => $transmissions,
			'default' => key($transmissions)
		);
		$bodies = static::bodyTypes();
		$mapped['body'] = array(
			'list' => $bodies,
			'default' => key($bodies)
		);
		return $mapped;
	}
}

?>