<?php

namespace sli_base\data\model\behavior;

use lithium\core\Libraries;
use lithium\util\Inflector;
use lithium\util\Set;
use lithium\core\ClassNotFoundException;

/**
 * Oh yes, has and belongs to many reltaionships, old school style.
 * 
 * @todo  - most of it
 * 
 * - find filter
 * - delete filter
 * - standardising key mapping and usage throughout, see if we can
 *   handle complex keys effectively
 * - query hanlding of passed in options
 * - support for join models as opposed to direct querries
 * - try hook in with relationship hanlding more 'natively' if possible
 * - possibly look at updating directly from the entity as opposed to
 *   just passed in data keys
 */
class ManyToMany extends \sli_base\data\model\Behavior {
		
	protected static $_settings = array(
		'bind' => array()
	);
	
	protected static $_binding = array(
		'to', 'key', 'assoc_key', 'source', 'fieldName', 'conditions', 'fields', 'order', 'limit'
	);
	
	protected static function _apply($model, $settings) {
		$settings = $settings + static::$_settings;
		$_bind = !empty($settings['bind']) ? (array) $settings['bind'] : array();
		$settings['bind'] = array();
		if (!empty($_bind)) {
			foreach ($_bind as $name => $binding) {
				if (!is_array($binding)) {
					$name = $binding;
					$binding = array();
				}
				$settings['bind'][$name] = static::_bind($model, $name, $binding);
			}
		}
		return $settings;
	}
	
	public static function saveFilter($model, $params, $chain) {
		if ($save = $chain->next($model, $params, $chain)) {
			$data = $params['data'];
			$entity = $params['entity'];
			static::saveAssociated($entity, $data);
		}
		return $save;
	}
	
	public static function saveAssociated($entity, $data = null, array $options = array()) {
		$model = $entity->model();
		$settings = static::_settings($model);
		if (isset($options['name'])) {
			$binding = $settings['bind'][$options['name']];
			$name = $options['name'];
		} else {
			foreach ($settings['bind'] as $name => $binding) {
				static::saveAssociated($entity, $data, compact('name') + $options);
			}
			return;
		}
		$field = $binding['fieldName'];
		$bindingData = isset($data[$field]) ? $data[$field] : null;
		if (isset($bindingData)) {
			$associated = array_filter($bindingData);
			$current = static::findAssociated($entity, compact('name') + array('results' => false));
			$delete = $create = array();
			if ($current) {
				if (empty($associated)) {
					$delete = $current;
				} else {
					$akey = current($binding['assoc_key']);
					foreach ($current as $row) {
						if (!in_array($row[$akey], $associated)) {
							$delete[] = $row;
						} else {
							$index = array_search($row[$akey], $associated);
							unset($associated[$index]);
						}
					}
				}
			}
			$key = array_flip(array_combine($entity->key(), $binding['key']));			
			foreach ($associated as $id) {
				$akey = array_combine($binding['assoc_key'], (array)$id);
				$create[] = $key + $akey;
			}
			$queryOptions = array(
				'type' => 'create',
				'source' => $binding['source'],
				'name' => Inflector::camelize($binding['source']),
			);
			$connection = $model::connection();
			foreach ($create as $data) {
				$query = $model::invokeMethod('_instance', array('query', compact('data') + $queryOptions));
				$connection->create($connection->renderCommand($query));
			}
			if ($delete) {
				$conditions = array();
				foreach ($delete as $record) {
					$key = array_intersect_key($record, array_flip($binding['assoc_key']));
					$key+= array_intersect_key($record, array_flip($binding['key']));	
					$conditions[]['and'] = $key;	
				}
				$queryOptions = array(
					'type' => 'delete',
					'conditions' => array('or' => $conditions)
				) + $queryOptions;
				$query = $model::invokeMethod('_instance', array('query', $queryOptions));
				$connection->delete($query);
			}
		} else {
			//save from entity... maybe.
		}
		
	}
	
	public static function deleteFilter($model, $params, $chain) {
		return $chain->next($model, $params, $chain);
	}
	
	public static function deleteAssociated($model, $options = array()) {}
	
	public static function findFilter($model, $params, $chain) {
		$bind = array();
		if (!empty($params['options']['with'])) {
			$settings = static::_settings($model);
			$exclude = array();
			foreach ($params['options']['with'] as $name => $options) {
				$key = $name;
				if (is_int($name)) {
					$name = $options;
					$options = array();
				}
				if (isset($settings['bind'][$name])) {
					$bind[] = compact('name') + $options;
					$exclude[] = $key;
				}
			}
			if (!empty($exclude)) {
				foreach ($exclude as $key) {
					unset($params['options']['with'][$key]);
				}
			}
		}
		$result = $chain->next($model, $params, $chain);
		if ($result && !empty($bind)) {
			$self = get_called_class();
			$apply = function(&$entity) use($self, $bind){
				foreach ($bind as $binding) {
					$self::findAssociated($entity, array('set' => true) + $binding);
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
		return $result;
	}
	
	public static function findAssociated($entity, array $options = array()) {
		$model = $entity->model();
		$settings = static::_settings($model);
		$options += array('set' => false);
		if (isset($options['name'])) {
			$binding = $settings['bind'][$options['name']];
			$name = $options['name'];
		} else {
			$values = array();
			foreach ($settings['bind'] as $name => $binding) {
				$values[$binding['fieldName']] = static::findAssociated($entity, compact('name') + $options);
			}
			return $values;
		}
		
		$key = array_flip(array_combine($entity->key(), $binding['key']));
		
		$queryOptions = array(
			'type' => 'read',
			'source' => $binding['source'],
			'name' => Inflector::camelize($binding['source']),
			'conditions' => $key
		);
		
		$query = $model::invokeMethod('_instance', array('query', $queryOptions));
		
		$result = $model::connection()->read($query, $queryOptions + array(
			'return' => 'array',
			'schema' => array_keys($model::connection()->describe($binding['source']))
		));
		
		$options += array('find' => 'all', 'conditions' => array(), 'results' => true);
		extract($options, EXTR_SKIP);
		$associated = array();
		if ($result) {
			if (!$results) {
				return $result;
			}
			foreach ($result as $row) {
				foreach ($binding['assoc_key'] as $key => $assoc) {
					$conditions[$key][] = $row[$assoc];
				}
			}
			$associated = $binding['to']::find($find, compact('conditions'));
		}
		if ($set) {
			$entity->{$binding['fieldName']} = $associated;
		}
		return $associated;
	}
	
	public static function bind($model, $name, $binding = array()) {
		$binding = static::_bind($model, $name, $binding);
		$settings = static::_settings($model);
		$settings['bind'][$name] = $binding;
		static::_settings($model, $settings);
		return $binding;
	}
	
	public static function unbind($model, $name) {
		$settings = static::_settings($model);
		if (($set = isset($settings['bind'][$name]) ? $settings['bind'][$name] : false)) {
			unset($settings['bind'][$name]);
			static::_settings($model, $settings);	
		}
		return $set;
	}
	
	protected static function _bind($from, $name, $config) {
		$config = compact('from', 'name') + $config + array_fill_keys(static::$_binding, null);
		if (!$config['to']) {
			$assoc = preg_replace("/\\w+$/", "", $config['from']) . $name;
			$config['to'] = Libraries::locate('models', $assoc);
		}
		if (!$config['key'] || !is_array($config['key'])) {
			$config['key'] = static::_keys($config['key'], $config['from']);
		}
		if (!$config['assoc_key'] || !is_array($config['assoc_key'])) {
			$config['assoc_key'] = static::_keys($config['assoc_key'], $config['to']);
		}
		if (!$config['source']) {
			$config['source'] = Inflector::tableize($config['from']::meta('name'));
			$config['source'].= '_' . Inflector::tableize($config['to']::meta('name'));
					
		}
		if (!$config['fieldName']) {
			$config['fieldName'] = lcfirst($name);
		}
		$self = get_called_class();
		$closure = function($entity, $options = array()) use($self, $name) {
			return $self::findAssociated($entity, compact('name') + $options);
		};
		$from::instanceMethods(array($config['fieldName'] => $closure));
		return $config;
	}
	
	protected static function _keys($key, $related) {
		if (class_exists($related)) {
			$from = $key ?: $related::key();
			$to = Inflector::underscore(Inflector::singularize($related::meta('name'))) . '_id';
			return array($from => $to);
		}
		throw new ClassNotFoundException("Related model class '{$related}' not found.");
	}
}

?>