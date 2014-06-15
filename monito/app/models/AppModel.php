<?php

namespace app\models;

use sli_base\util\filters\Behaviors;

class AppModel extends \lithium\data\Model {

	public static function __init(){
		static::_applyFilters();
	}

	protected static function _applyFilters() {
		Behaviors::apply(get_called_class(), 'Timestamped');
	}
}

?>