<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\data;

use lithium\util\Inflector;

class MockSource extends \lithium\tests\mocks\data\MockSource {

	public $records = array();

	protected $_mockItems = array(
		'id' => array('type' => 'integer'),
		'title' => array('type' => 'string'),
		'class' => array('type' => 'string')
	);

	protected $_mockItemOnes = array(
		'id' => array('type' => 'integer'),
		'flavour' => array('type' => 'string')
	);

	protected $_mockItemTwos = array(
		'id' => array('type' => 'integer'),
		'color' => array('type' => 'string')
	);

	protected $_posts = array(
		'id' => array('type' => 'integer'),
		'author_id' => array('type' => 'integer'),
		'title' => array('type' => 'string'),
		'body' => array('type' => 'text'),
		'created' => array('type' => 'datetime'),
		'updated' => array('type' => 'datetime')
	);

	public function create($query, array $options = array()) {
		if (!is_object($query)) {
			return false;
		}
		$entity =& $query->entity();
		$model = $entity->model();
		$rows = $this->dump($model);
		$insertId = empty($rows) ? 1 : end($rows) ? key($rows) + 1 : count($rows) + 1;
		$schema = $model::schema();
		foreach ($schema as $field => $settings) {
			if (!isset($entity->{$field})) {
				$entity->{$field} = !empty($settings['default']) ? $settings['default'] : null;
			}
		}
		$entity->set($query->data());
		$key = $entity->key();
		if ($key && ($id = reset($key))) {
			$insertId = $id;
		} else {
			$insertId = count($this->records) + 1;
		}
		$entity->sync($insertId);
		$data = array_intersect_key($entity->data(), $schema->fields());
		$this->records[$query->model()][$insertId] = $data;
		return true;
	}

	public function read($query, array $options = array()) {
		$model = $query->model();

		$links = array();

		$joins = $query->joins();
		$data = isset($this->records[$model]) ? $this->records[$model] : array();

		$conditions = array();
		if ($_conditions = $query->conditions()) {
			foreach ($_conditions as $field => $value) {
				$field = basename(str_replace('.', '/', $field));
				if ($model::schema($field)) {
					$conditions[$field] = $value;
				}
			}
		}
		$results = array();
		foreach ($data as $id => $record) {
			if ($conditions && array_diff($conditions, $record)) {
				continue;
			}
			$relationships = array();
			if ($relations = $model::relations()) {
				foreach ($relations as $alias => $join) {
					$to = $join->data('to');
					$field = $join->data('fieldName');
					//@todo: join on constraint
					//$constraint = $join->constraint();
					$key = $model::key();
					if (isset($this->records[$to][$id])) {
						$relationships[$field] = $to::create($this->records[$to][$id]);
					}
				}
			}
			$results[$id] = $model::create($record, array('relationships' => $relationships));
		}
		$class = 'set';
		$options = compact('class') + $options;
		return $model::create($results, $options);
	}

	public function update($query, array $options = array()) {
		if (!is_object($query)) {
			return false;
		}
		if ($query->entity()) {
			$query->entity()->sync();
			$model = $entity->model();
			$data = array_intersect_key($entity->data(), $schema);
			$this->records[$query->model()][$entity->id()] = $data;
		}
		return true;
	}

	public function delete($query, array $options = array()) {
		if (!is_object($query)) {
			return false;
		}
		$model = $query->model();
		$conditions = $query->conditions();
		if (isset($conditions['id'])) {
			unset($this->records[$model][$conditions['id']]);
		} else {
			unset($this->records[$model]);
		}
		return true;
	}

	public function dump($model = null) {
		if (isset($model)) {
			return isset($this->records[$model]) ? $this->records[$model] : array();
		}
		return $this->records;
	}

	public function flush() {
		$this->records = array();
	}

	public function describe($entity, $schema = array(), array $meta = array()) {
		$source = '_' . Inflector::camelize($entity, false);
		$fields = isset($this->$source) ? $this->$source : $schema;
		return $this->_instance('schema', compact('fields'));
	}
}

?>