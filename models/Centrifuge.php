<?php

namespace centrifuge\models;

use sli_base\util\filters\Behaviors;
use sli_scaffold\core\Scaffold;
use sli_scaffold\models\Scaffolds;

class Centrifuge extends \lithium\data\Model {
	
	protected static $_actsAs = array(
		'Timestamped' => array(
			'format' => 'U',
			'update' => 'modified'
		)
	);
	
	public static function getScaffoldFields($binding) {
		$schema = static::schema();
		unset($schema['created'], $schema['modified']);
		return array_keys($schema);
	}
	
	public static function getScaffoldFormFields($binding) {
		$class = get_called_class();
		return Scaffolds::mapSchemaFields($class);
	}
	
	public static function __init() {
		static $options = array();
		if (get_called_class() == __CLASS__) {
			$options = static::__setup() ?: $options;
		} else {
			static::config($options);
			static::_applyFilters();
		}
	}
	
	private static function __setup(){
		if (static::_isBase(__CLASS__)) {
			return;
		}
		static::_isBase(__CLASS__, true);
		
		$mapping = Scaffolds::getFieldMapping('default');
		$mapping['created'] = $mapping['modified'] = array('type' => 'hidden');
		Scaffolds::setFieldMapping('default', $mapping);
		
		return static::_filter(__FUNCTION__, array(), function($self, $params){
			return $params;
		});
	}
	
	protected static function _applyFilters() {
		$actsAs = static::$_actsAs;
		foreach (static::_parents() as $parent) {
			$parentConfig = get_class_vars($parent);
			if (isset($parentConfig["_actsAs"])) {
				$actsAs += $parentConfig["_actsAs"];
			}
			if ($parent == __CLASS__) {
				break;
			}
		}
		$class = get_called_class();
		Behaviors::apply($class, $actsAs);
	}
}

?>