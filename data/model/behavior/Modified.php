<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

/**
 * The `Modified` model behavior.
 *
 * This behavior applies filters for the modification of configured record
 * fields on create(after), validates(before), save(before) and find(after)
 * operations.
 *
 * @see sli_base\core\Behavior
 * @see sli_base\core\Behaviors
 */
class Modified extends \sli_base\data\model\Behavior {

	/**
	 * Configuration
	 *
	 * - `fields`: array fields to modify and the modifier callbacks indexed
	 * 			   by action.
	 * 		field => array(action => callable)
	 * 		field => array(action => array(callable, arg, arg...))
	 * 		field => array(action => array(
	 * 			call => callable,
	 * 			args => args,
	 * 			map => alias key to map the filtered value to,
	 * 			_map => alias key to backup the existing value to
	 * 		))
	 * - `check`: boolean check fields against model schema and remove non existing from
	 * 			  fields config
	 *
	 * @var array
	 */
	protected static $_settings = array(
		'fields' => array()
	);

	/**
	 * Configured modifier types
	 *
	 * @see sli_base\data\model\behavior\Modified::modifiers()
	 * @var array
	 */
	protected static $_modifiers = array();

	/**
	 * Control if the behavior should only apply modifier filters configured.
	 *
	 * @var boolean
	 */
	protected static $_modifierOnly = false;

	/**
	 * After Create filter.
	 * Applies configured filters to created entity.
	 *
	 * @see lithium\data\Model::create();
	 * @param array $settings settings for the binding
	 * @param string $model model behavior is bound to
	 * @param object $entity result of Model::create()
	 * @return lithium\data\Entity
	 */
	public static function createAfterFilter($model, $entity, $settings) {
		$fields = static::_fields($settings);
		$data = $entity->data();
		$applied = static::modify($data, 'create', $fields);
		if ($modified  = static::_diff($applied, $data)) {
			$entity->set($modified);
		}
		return $entity;
	}

	/**
	 * Before Save filter.
	 * Applies configured filters to data prior to saving.
	 *
	 * @see lithium\data\Model::save();
	 * @param array $settings settings for the binding
	 * @param string $model model behavior is bound to
	 * @param array $params Model::save() params
	 * @return array params
	 */
	public static function saveBeforeFilter($model, $params, $settings) {
		//apply to record
		$entity =& $params['entity'];
		$fields = static::_fields($settings);
		if (!($data = $params['data'])) {
			$data = $entity->data();
		}
		$applied = static::modify($data, 'save', $fields);
		if ($modified  = static::_diff($applied, $data)) {
			$params['data'] = $modified + $data;
		}
		return $params;
	}

	/**
	 * Before Validates filter.
	 * Applies configured filters to entity prior to validating.
	 *
	 * @see lithium\data\Model::validates();
	 * @param array $settings settings for the binding
	 * @param string $model model behavior is bound to
	 * @param array $params Model::validates() params
	 * @return array params
	 */
	public static function validatesBeforeFilter($model, $params, $settings) {
		//apply to record
		$entity =& $params['entity'];
		$fields = static::_fields($settings);
		$data = $entity->data();
		$applied = static::modify($data, 'validates', $fields);
		if ($modified  = static::_diff($applied, $data)) {
			$entity->set($modified);
		}
		return $params;
	}

	/**
	 * After Find filter.
	 * Applies configured filters to entity/collection after find.
	 *
	 * @see lithium\data\Model::find();
	 * @param array $settings settings for the binding
	 * @param string $model model behavior is bound to
	 * @param array $result result of Model::find()
	 * @return array params
	 */
	public static function findAfterFilter($model, $result, $settings) {
		if ($result) {
			$self = get_called_class();
			$apply = function(&$result) use($self, $settings){
				$fields = $self::invokeMethod('_fields', array($settings));
				$data = $result->data();
				$args = array($data, 'find', $fields);
				$applied = $self::invokeMethod('modify', $args);
				if ($modified  = $self::invokeMethod('_diff', array($applied, $data))) {
					$result->set($modified);
				}
				return $result;
			};
			if ($result instanceOf \lithium\data\Entity) {
				$apply($result);
			} elseif ($result instanceOf \lithium\data\Collection) {
				foreach ($result as $record) {
					$apply($record);
				}
			}
		}
		return $result;
	}

	/**
	 * Modifier setter/getter. Allows configuration of filter sets for
	 * convenience.
	 *
	 * For example to apply strtoupper on find operations
	 * {{{
	 * Modified::modifiers(array('upper' => array(
	 * 	'find' => 'strtoupper'
	 * )));
	 * }}}
	 *
	 * The behavior can then be applied using the modifier key, this will
	 * result in strtoupper being applied to the `name` field after find.
	 * {{{
	 * Modified::apply($model, array('fields' => array(
	 * 	'name' => 'upper'
	 * )));
	 * //or
	 * Modified::apply($model, array('upper' => array(
	 * 	'name','others...'
	 * )));
	 * }}}
	 *
	 * @param array $modifiers
	 * @return array all modifiers
	 */
	public static function modifiers($modifiers = array()) {
		if ($modifiers && $modifiers = array_diff_key($modifiers, static::$_settings)) {
			static::$_modifiers = $modifiers + static::$_modifiers;
		}
		return static::$_modifiers;
	}

	/**
	 * Format and apply configured filters to a data array for a given action.
	 *
	 * This method is used internally to modify datasets, but is exposed
	 * publicly to allow for directly calling mapped filters.
	 *
	 * @param array $data
	 * @param string $action
	 * @param array $fields
	 * @return array data with filters applied to values
	 */
	public static function modify($data, $action, $fields) {
		$filters = static::_fieldFilters($fields, $action);
		foreach ($filters as $field => $filter) {
			static::_modfiyField($filter, $field, $data);
		}
		return $data;
	}

	protected static function _diff($applied, $data) {
		$diff = array();
		foreach ($applied as $field => $value) {
			if (!array_key_exists($field, $data) || $value !== $data[$field]) {
				$diff[$field] = $value;
			}
		}
		return $diff;
	}

	/**
	 * Extract fields to modify from `fields` key, and also from any modifier
	 * keys configured.
	 *
	 * @param array $settings
	 * @return array fields & filters
	 */
	protected static function _fields($settings) {
		$fields = $settings['fields'];
		if($modifiers = static::modifiers()) {
			foreach (array_keys($modifiers) as $modifier) {
				if (isset($settings[$modifier])) {
					$fields += array_fill_keys((array) $settings[$modifier], $modifier);
				}
			}
		}
		return $fields;
	}

	/**
	 * Extract a set of fields and filters to be applied for the current action
	 *
	 * @param array $fields
	 * @param string $action
	 * @return array fields & filters
	 */
	protected static function _fieldFilters($fields, $action) {
		$filters = array();
		$modifiers = static::modifiers();
		foreach ($fields as $field => $setting) {
			if (is_string($setting) && isset($modifiers[$setting])) {
				$setting = $modifiers[$setting];
			} elseif(static::$_modifierOnly) {
				continue;
			}
			if (is_array($setting) && !empty($setting[$action])) {
				$filters[$field] = $setting[$action];
			}
		}
		return $filters;
	}

	/**
	 * Apply filter to a field
	 *
	 * @param mixed $filter
	 * @param mixed $field
	 * @param array $data
	 * @return null
	 */
	protected static function _modfiyField($filter, $field, &$data) {
		if (!array_key_exists($field, $data)) {
			$value = null;
		} else {
			$value = $data[$field];
		}
		$map = $field;
		$function = $_map = null;
		$args = array();
		switch (true) {
			//callable
			case is_callable($filter):
				$function = $filter;
				break;
			//full filter options
			case is_array($filter) && isset($filter['call']):
				$function = $filter['call'];
				$args = isset($filter['args']) ? $filter['args'] : array();
				$map = isset($filter['map']) ? $filter['map'] : $field;
				$_map = isset($filter['_map']) ? $filter['_map'] : null;
				break;
			//array callable [0] with args [1,n...]
			case is_array($filter):
				$function = array_shift($filter);
				$args = $filter;
				break;
		}
		if ($function) {
			$key = array_search('{:field}', $args, true);
			if ($key !== false) {
				$args[$key] = $value;
			} else {
				array_unshift($args, $value);
			}

			$key = array_search('{:data}', $args, true);
			if ($key !== false) {
				$args[$key] =& $data;
			}

			if ($_map) {
				$data[$_map] = $value;
			}
			$data[$map] = call_user_func_array($function, $args);
		}
	}
}

?>