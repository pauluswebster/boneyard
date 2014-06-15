<?php
namespace monito\models;

class Services extends AppModel {

	public $hasMany = array('Connections');

	public static $scaffoldFields = array(
		'name'
	);
}

?>