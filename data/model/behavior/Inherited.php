<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

use lithium\util\Inflector;

class Inherited extends \sli_base\data\model\Behavior {

	protected static $_settings = array(
		'base' => null,
		'parents' => array(),
		'transient' => array(),
		'prefix' => 'Inherited'
	);

	protected static function _apply($class, $settings) {
		$settings = parent::_apply($class, $settings);
		$parents = $class::invokeMethod('_parents');
		if (empty($settings['base'])) {
			$baseClasses = array_filter($parents, function($parent) use ($class){
				return $class::invokeMethod('_isBase', array($parent));
			});
			$base = $class;
			while ($parent = array_shift($parents)) {
				if (in_array($parent, $baseClasses)) {
					break;
				}
				$base = $parent;
			}
			$settings['base'] = $base;
		}
		
		if ($settings['base'] != $class) {
			$parents = array_values($class::invokeMethod('_parents'));
			$index = array_search($settings['base'], $parents);
			$settings['parents'] = array_fill_keys(array_slice($parents, 0, $index + 1), array());
		}
		$base = $settings['base'];
		foreach ($settings['parents'] as $parent => &$config) {
			$assocName = $settings['prefix'] . static::_name($parent);
			$config += array(
				'key' => $base::key(),
				'to' => $parent,
				'name' => $assocName,
				'fieldName' => Inflector::underscore($assocName)
			);
			$class::bind('belongsTo', $config['name'], $config);
		}
		return $settings;
	}

	public static function schema($model) {
		$self = get_called_class();
		$settings = static::settings($model);
		$schema = $model::schema();
		foreach ($settings['parents'] as $parent => $config) {
			$schema = $parent::schema() + $schema;
		}
		return $schema;
	}

	/**
	 * @todo address recursion, finalise inherited flag
	 */
	public static function createFilter($model, $params, $chain, $settings) {
		$self = get_called_class();
		$inherited = true;
		if (isset($params['options']['inherited'])) {
			$inherited = $params['options']['inherited'];
		}
		if ($inherited && $settings['parents']) {
			$relationships = array();
			if (!isset($params['options']['relationships'])) {
				$params['options']['relationships'] = array();
			}
			foreach ($settings['parents'] as $parent => &$config) {
				$relationships[$config['fieldName']] = $parent::create($params['data'], array('inherited' => false));
				$params['data'] = $relationships[$config['fieldName']]->data() + $params['data'];
			}
			$params['options']['relationships'] = $relationships;
			$params['options']['schema'] = static::schema($model);
		}
		$params['data']['class'] = static::_name($model);
		$entity = $chain->next($model, $params, $chain);
		return $entity;
	}

	public static function saveFilter($model, $params, $chain, $settings) {
		extract($params);
		//set data to record
		if (!empty($data)) {
			$entity->set($data);
			$data = array();
		}
		//create parent record first
		if (isset($entity->$settings['fieldName'])) {
			$parent = $entity->$settings['fieldName'];
			$parentModel = $parent->model();
			$parentData = $entity->data();
			unset($parentData[$settings['fieldName']]);
			if (!$parent->exists()) {
				unset($parentData[$parentModel::meta('key')]);
			}
			$parent->set($parentData);
			$parent->save();
			if (!$entity->exists()) {
				$entity->set($parent->key());
			}
		}
		$params = compact(array_keys($params));
		return $chain->next($model, $params, $chain);
	}

	public static function validateFilter($model, $params, $chain) {
		$self = get_called_class();
		$settings =& static::$__settings[$self][$model];
		//validate all
		return $chain->next($model, $params, $chain);
	}

	/**
	 * 
	 * Enter description here ...
	 * 
	 * @todo find filtering on class
	 * @todo optimize to single query for after find of multiple records
	 * 
	 * @param unknown_type $model
	 * @param unknown_type $params
	 * @param unknown_type $chain
	 * @param unknown_type $settings
	 */
	public static function findFilter($model, $params, $chain, $settings) {
		$class = static::_name($model);
		$base = static::_name($settings['base']);
		
		$mapConditions = function(&$conditions) use($class){
			$mapped = array();
			foreach ($conditions as $field => $condition) {
				if (strpos($field, '.') === false) {
					$field = "{$class}.{$field}";
				}
				$mapped[$field] = $condition;
			}
			$conditions = $mapped;
		};
		
		if (isset($params['options']['conditions'])) {
			$mapConditions($params['options']['conditions']);
		} else {
			$params['options']['conditions'] = array();
		}
		
		if ($settings['parents']) {
			if (!isset($params['options']['with'])) {
				$params['options']['with'] = array();
			}
			foreach ($settings['parents'] as $parent => $config) {
				$params['options']['with'][] = $config['name'];
			}
			$params['options']['conditions']["{$config['name']}.class"] = $class;
		} else {
			$params['options']['conditions']["{$base}.class"] = $class;
		}
		
		$result = $chain->next($model, $params, $chain);

		if ($settings['parents']) {
			if ($result) {
				$self = get_called_class();
				$apply = function(&$entity) use($model, $settings, $class){
					$data = $entity->data();
					foreach ($settings['parents'] as $parent => $config) {
						if (!empty($data[$config['fieldName']])) {
							$entity->set($data[$config['fieldName']]);
						}
					}
				};
				if ($result instanceOf \lithium\data\Entity) {
					$apply($result);
				} elseif ($result instanceOf \lithium\data\Collection) {
					foreach ($result as $entity) {
						$apply($entity);
					}
				}
			}
		}
		return $result;
	}

	public static function deleteFilter($model, $params, $chain, $settings) {
		if ($delete = $chain->next($model, $params, $chain)) {
			extract($params);
			if (isset($entity->$settings['fieldName'])) {
				$inherited = $entity->$settings['fieldName'];
				if (!$inherited->delete()) {
					$entity->save();
					$delete = false;
				}
			}
		}
		return $delete;
	}

	protected static function _isBase($model) {
		$self = get_called_class();
		$settings = static::settings($model);
		return $model == $settings['base'];
	}
	
	protected static function _name($class) {
		return basename(str_replace('\\', '/', $class));
	}
	
	/**
	 * @todo, implement or trash
	 */
	protected static function _isTransient($model, $settings) {}
}
?>
