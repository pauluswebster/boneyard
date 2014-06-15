<?php
namespace monito\models;

class Connections extends AppModel {

	public $belongsTo = array('Services');

	public $hasMany = array('Resources');

	public static $scaffoldFields = array(
		'name'
	);
}

?>