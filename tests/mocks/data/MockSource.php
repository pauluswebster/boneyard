<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\data;

class MockSource extends \lithium\tests\mocks\data\MockSource {

	public $records = array();

	private $_insertId = 0;

	public function create($query, array $options = array()) {
		$insertId = $this->_insertId + 1;
		if (!is_object($query)) {
			return false;
		}
		$entity =& $query->entity();
		$schema = $entity->schema();
		foreach ($schema as $field => $settings) {
			if (!isset($entity->{$field})) {
				$entity->{$field} = !empty($settings['default']) ? $settings['default'] : null;
			}
		}
		$entity->sync($insertId);
		$model = $entity->model();
		$data = array_intersect_key($entity->data(), $schema);
		$this->records[$query->model()][$insertId] = $data;
		return true;
	}

	public function read($query, array $options = array()) {
		$model = $query->model();
		$data = isset($this->records[$model]) ? $this->records[$model] : array();
		foreach ($data as &$record) {
			$record = $this->item($model, $record, array('class' => 'entity'));
		}
		$class = 'set';
		$options = compact('class') + $options;
		return $this->item($model, $data, $options);
	}

	public function update($query, array $options = array()) {
		if (!is_object($query)) {
			return false;
		}
		if ($query->entity()) {
			$query->entity()->sync();
			$model = $entity->model();
			$data = array_intersect_key($entity->data(), $schema);
			$this->_records[$query->model()][$entity->id()] = $data;
		}
		return true;		
	}

	public function flush() {
		$this->_records = array();
	}
}

?>