<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model;

/**
 * The `model\Behavior' class provides common utility methods
 * useful for model behaviors.
 *
 * @see sli_base\core\Behavior
 */
class Behavior extends \sli_base\core\Behavior {

	/**
	 * Checks the model schema for the existence of the fields
	 *
	 * @param string $model model class name
	 * @param array $fields array of field names to check for
	 * @return array values from $fields that exist in the $model schema
	 */
	protected static function _checkSchema($model, array $fields){
		$schema = $model::schema();
		return array_intersect($fields, array_keys($schema));
	}

	/**
	 * Extract a data subset from an array/entity
	 *
	 * @param mixed $entity Entity instance or array data
	 * @param array $fields fields to extract from data
	 * @return mixed array fields, null
	 */
	protected static function _extractFields($entity, $fields) {
		if ($data = is_object($entity) ? $entity->data() : $entity) {
			return array_intersect_key($data, $fields);
		}
	}
}

?>