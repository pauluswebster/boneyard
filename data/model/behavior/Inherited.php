<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

use lithium\util\Inflector;

/**
 * The `Inherited` model behavior.
 * 
 * This behavior provides support for inheritance for your models.
 * 
 * @todo properly adhere to the created relationships for observing keys etc
 * @todo find needs a lot of work to parse field based options
 * @todo discriminator config
 * @todo transient handling (inherited unbound/base classes)
 * @todo improvements for single table inheritance
 * @todo notes throughout
 */
class Inherited extends \sli_base\data\model\Behavior {

	protected static $_settings = array(
		'base' => null,
		'parents' => array(),
		'transient' => array(),
		'prefix' => 'Inherited'
	);

	/**
	 * Configure the behavior binding
	 */
	protected static function _apply($class, $settings) {
		$settings = parent::_apply($class, $settings);
		$parents = $class::invokeMethod('_parents');
		if (empty($settings['base']) || !in_array($settings['base'], $parents)) {
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
		$settings['parents'] = array();
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

	/**
	 * Get combined schema of inherited models
	 * 
	 * @param string $model
	 * @return array
	 */
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
	 * Create inherited records
	 */
	public static function createFilter($model, $params, $chain, $settings) {
		$self = get_called_class();
		if (isset($params['options']['inherited'])) {
			$inherited = $params['options']['inherited'];
		} else {
			$inherited = static::_name($model);
			$params['options']['inherited'] = $inherited;
		}
		if ($inherited == static::_name($model) && $settings['parents']) {
			$relationships = array();
			if (!isset($params['options']['relationships'])) {
				$params['options']['relationships'] = array();
			}
			foreach ($settings['parents'] as $parent => &$config) {
				$relationships[$config['fieldName']] = $parent::create($params['data'], $params['options']);
				$params['data'] = $relationships[$config['fieldName']]->data() + $params['data'];
			}
			$params['options']['relationships'] = $relationships;
		}
		if ($inherited) {
			$params['data']['class'] = $inherited;
		}
		return $chain->next($model, $params, $chain);
	}

	/**
	 * Save inherited records
	 * 
	 * @todo halt cascade on failure?
	 * @todo map keys based on relationship
	 */
	public static function saveFilter($model, $params, $chain, $settings) {
		if (isset($params['options']['inherited'])) {
			$inherited = $params['options']['inherited'];
		} else {
			$inherited = static::_name($model);
			$params['options']['inherited'] = $inherited;
		}
		if ($inherited == static::_name($model) && $settings['parents']) {
			$entity =& $params['entity'];
			foreach (array_reverse($settings['parents'], true) as $parent => $config) {
				$record =& $entity->{$config['fieldName']};
				$data = $params['data'];
				if (!$record->exists()) {
					unset($data[$parent::key()]);
				}
				$record->save($data, $params['options']);
				if ($parent == $settings['base']) {
					$params['data'] = $record->key() + ($params['data'] ?: array());
				}
			}
		}
		return $chain->next($model, $params, $chain);
	}

	/**
	 * Execute validation across inherited models
	 * 
	 * @todo
	 */
	public static function validateFilter($model, $params, $chain, $settings) {
		return $chain->next($model, $params, $chain);
	}

	/**
	 * Bind inheritance to the query, parse options
	 * 
	 * @todo extend field based option support (conditions etc)
	 */
	public static function findFilter($model, $params, $chain, $settings) {
		if (isset($params['options']['inherited'])) {
			if (!$params['options']['inherited']) {
				return $chain->next($model, $params, $chain);
			}
		}
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

	/**
	 * Delete inheritance records
	 * 
	 * @todo halt cascade on failure?
	 */
	public static function deleteFilter($model, $params, $chain, $settings) {
		if (isset($params['options']['inherited'])) {
			$inherited = $params['options']['inherited'];
		} else {
			$inherited = static::_name($model);
			$params['options']['inherited'] = $inherited;
		}
		if ($inherited == static::_name($model) && $settings['parents']) {
			$entity =& $params['entity'];
			foreach ($settings['parents'] as $parent => $config) {
				if ($record =& $entity->{$config['fieldName']}) {
					$record->delete(array('model' => $record->model()) + $params['options']);	
				}
			}
		}
		return $chain->next($model, $params, $chain);
	}
	
	/**
	 * Gets just the class name portion of a fully-name-spaced class name
	 *
	 * @return string
	 */
	protected static function _name($class) {
		return basename(str_replace('\\', '/', $class));
	}
	
	/**
	 * Check if model is configured as the base
	 * 
	 * @todo probably trash
	 */
	protected static function _isBase($model) {
		$self = get_called_class();
		$settings = static::settings($model);
		return $model == $settings['base'];
	}
	
	/**
	 * Check if a model is transient in the inheritance chain
	 * 
	 * @todo, implement or trash
	 */
	protected static function _isTransient($model, $settings) {}
}
?>
