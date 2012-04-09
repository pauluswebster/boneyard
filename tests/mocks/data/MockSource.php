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

	public function create($query, array $options = array()) {
		if (!is_object($query)) {
			return false;
		}
		$entity =& $query->entity();
		$model = $entity->model();
		$rows = $this->dump($model);
		$insertId = empty($rows) ? 1 : end($rows) ? key($rows) + 1 : count($rows) + 1;
		$schema = $entity->schema();
		foreach ($schema as $field => $settings) {
			if (!isset($entity->{$field})) {
				$entity->{$field} = !empty($settings['default']) ? $settings['default'] : null;
			}
		}
		$entity->set($query->data());
		$key = $entity->key();
		if ($id = reset($key)) {
			$insertId = $id;
		}
		$entity->sync($insertId);
		$data = array_intersect_key($entity->data(), $schema);
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
			if ($joins) {
				foreach ($joins as $join) { 
					$to = $join->model();
					$alias = $join->alias();
					$field = Inflector::underscore($alias);
					//@todo: join on constraint
					//$constraint = $join->constraint();
					//$key = $model::key();
					if (isset($this->records[$to][$id])) {
						$relationships[$field] = $this->item($to, $this->records[$to][$id], array('class' => 'entity'));
					}	
				}
			}
			$results[$id] = $this->item($model, $record, array('class' => 'entity', 'relationships' => $relationships));
		}
		$class = 'set';
		$options = compact('class') + $options;
		return $this->item($model, $results, $options);
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
}

?>