<?php

namespace app\models;

class AppModel extends \lithium\data\Model {

	public static function __init() {
		static::_isBase(__CLASS__, true);
		parent::__init();
	}
	
}

?>