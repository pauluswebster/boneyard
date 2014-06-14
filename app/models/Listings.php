<?php

namespace app\models;

use sli_base\util\filters\Behaviors;
use sli_scaffold\models\Scaffolds;

class Listings extends AppModel {

	/**
	 * Initialize model
	 */
	public static function __init(){
		$class = get_called_class();
		if ($class == __CLASS__) {
			$self = $class::_object();
			$self->belongsTo[] = 'Categories';
		}
		parent::__init();
		static::_applyFilters();
	}

	/**
	 * Apply filters to model
	 */
	protected static function _applyFilters() {
		$class = get_called_class();

		$behaviors = array(
			'Inherited' => array('base' => __CLASS__)
		);
		if ($class == __CLASS__) {
			$behaviors['Timestamped'] = array('format' => 'U');
		}
		Behaviors::apply($class, $behaviors);
	}

	public static function getScaffoldFields() {
		$class = get_called_class();
		$inherited = Behaviors::locate($class, 'Inherited');
		$schema = $inherited::schema($class);
		return $schema->names();
	}

	public static function getScaffoldFormFields() {
		$class = get_called_class();
		$inherited = Behaviors::locate($class, 'Inherited');
		$schema = $inherited::schema($class);
		return Scaffolds::mapSchemaFields($class, null, $schema->fields());
	}
}

?>