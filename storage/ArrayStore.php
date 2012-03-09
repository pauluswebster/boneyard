<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\storage;

use sli_base\util\Store;

/**
 * The `ArrayStore` storage class provides simple array storage and access via
 * standard array access & dot-delimeted array paths.
 *
 * @see sli_base\util\Store
 */
class ArrayStore extends \lithium\core\Object implements \ArrayAccess {

	protected $_autoConfig = array('data');

	protected $_data = array();

	protected function _init() {
		parent::_init();
		unset($this->_config['data']);
	}

	/**
	 * Write values to path
	 *
	 * @param mixed $path string path to write | array paths & values to write
	 * @param mixed $value array value to write to $path if it is string path
	 * @param boolean $merge true merge pased in $value with $array
	 * @return array
	 */
	public function set($path, $value = null, $merge = false) {
		$this->_data = Store::set($this->_data, $path, $value, $merge);
		return $this->_data;
	}

	/**
	 * Write values by merging with current values at the specified path
	 *
	 * @param mixed $path
	 * @param mixed $value null | value to merge with the config located at $path
	 * @return array
	 */
	public function merge($path, $value= null) {
		$this->_data = Store::merge($this->_data, $path, $value);
		return $this->_data;
	}

	/**
	 * Read values from path
	 *
	 * @param mixed $path dot-delimited string path | array paths
	 * @return mixed
	 */
	public function get($path = null) {
		if ($path === null) {
			return $this->_data;
		}
		return Store::get($this->_data, $path);
	}

	/**
	 * Read all values
	 *
	 * @param mixed $path dot-delimited string path | array paths
	 * @return array
	 */
	public function data() {
		return $this->_data;
	}

	/**
	 * Get keys at array path
	 *
	 * @param mixed $path
	 * @return array
	 */
	public function keys($path = null){
		return Store::keys($this->_data, $path);
	}

	public function keyExists($path = null){
		return Store::keyExists($this->_data, $path);
	}

	/**
	 * Extract values maintaining array structure
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return mixed array data if set
	 */
	public function extract($path) {
		return Store::extract($this->_data, $path);
	}

	/**
	 * Filter data at path
	 *
	 * @param mixed $filter callable
	 * @param string $path
	 * @return array
	 */
	public function filter($filter, $path = null) {
		$filtered = Store::filter($this->_data, $path, $filter);
		return $this->set($path, $filtered);
	}

	/**
	 * Apply filter callback to data at path
	 *
	 * @param mixed $filter callable
	 * @param string $path
	 * @return array
	 */
	public function apply($filter, $path = null) {
		$filtered = Store::apply($this->_data, $path, $filter);
		return $this->set($path, $filtered);
	}

	/**
	 * Remove value by path
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return array
	 */
	public function delete($path = null) {
		$this->_data = Store::delete($this->_data, $path);
		return $this->_data;
	}

	/**
	 * ArrayAccess
	 *
	 * @param offset
	 */
	public function offsetExists($offset) {
		return $this->keyExists($offset);
	}

	/**
	 * ArrayAccess
	 *
	 * @param offset
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * ArrayAccess
	 *
	 * @param offset
	 * @param value
	 */
	public function offsetSet($offset, $value) {
		return $this->set($offset, $value);
	}

	/**
	 * ArrayAccess
	 *
	 * @param offset
	 */
	public function offsetUnset($offset) {
		return $this->delete($offset);
	}
}

?>