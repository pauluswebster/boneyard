<?php
namespace Sli\Filter;

class Filters implements \ArrayAccess, \Iterator, \Countable {

	protected $_filters = array();

	protected $_valid = false;

	protected static $_methodFilters = array();

	public function __construct(array $options = array()) {
		if (!empty($options['data'])) {
			$this->_filters = $options['data'];
		}
	}

	public static function apply($method, $filter = null) {
		if (is_array($method)) {
			list($class, $_method) = $method;
			$method = $_method;
		} else {
			list($class, $method) = explode('::', $method);
		}
		if (is_object($class)) {
			$class->_methodFilters[$method][] = $filter;
		} else {
			static::$_methodFilters[$class][$method][] = $filter;
		}
	}

	public static function omit($method, $filter = null) {
		if (is_array($method)) {
			list($class, $_method) = $method;
			$method = $_method;
		} else {
			list($class, $method) = explode('::', $method);
		}
		if (is_object($class)) {
			if (!empty($class->_methodFilters[$method])) {
				if ($filter) {
					$index = array_search($filter, $class->_methodFilters[$method]);
					if ($index) {
						unset($class->_methodFilters[$method][$index]);
					}
				} else {
					$class->_methodFilters[$method] = array();
				}
			}
		} else {
			if (!empty(static::$_methodFilters[$class][$method])) {
				if ($filter) {
					$index = array_search($filter, static::$_methodFilters[$class][$method]);
					if ($index) {
						unset(static::$_methodFilters[$class][$method][$index]);
					}
				} else {
					static::$_methodFilters[$class][$method] = array();
				}
			}
		}
	}

	public static function run($method, $params, $callback, $filters = array()) {
		if (is_array($method)) {
			list($class, $_method) = $method;
			$method = $_method;
		} else {
			list($class, $method) = explode('::', $method);
		}
		$_filters = array();
		if (is_object($class)) {
			if (!empty($class->_methodFilters) && !empty($class->_methodFilters[$method])) {
				$_filters = $class->_methodFilters[$method];
			}
		} else {
			if (!empty(static::$_methodFilters[$class][$method])) {
				$_filters = static::$_methodFilters[$class][$method];
			}
		}
		if (empty($_filters) && empty($filters)) {
			return $callback($class, $params, null);
		}
		$data = array_merge($_filters, $filters, array($callback));
		return static::_run($class, $params, compact('data', 'class', 'method'));
	}

	protected static function _run($class, $params, array $options = array()) {
		$defaults = array('class' => null, 'method' => null, 'data' => array());
		$options += $defaults;
		$chain = new Filters($options);
		$next = $chain->rewind();
		return call_user_func($next, $class, $params, $chain);
	}


	public function offsetExists($offset) {
		return isset($this->_filters[$offset]);
	}

	public function offsetGet($offset) {
		return $this->_filters[$offset];
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			return $this->_filters[] = $value;
		}
		return $this->_filters[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->_filters[$offset]);
		prev($this->_filters);
	}

	public function rewind() {
		$this->_valid = !(reset($this->_filters) === false && key($this->_filters) === null);
		return current($this->_filters);
	}

	public function end() {
		$this->_valid = !(end($this->_filters) === false && key($this->_filters) === null);
		return current($this->_filters);
	}

	public function valid() {
		return $this->_valid;
	}

	public function current() {
		return current($this->_filters);
	}

	public function key() {
		return key($this->_filters);
	}

	public function prev() {
		if (!prev($this->_filters)) {
			end($this->_filters);
		}
		return current($this->_filters);
	}

	public function next($self = null, $params = null, $chain = null) {
		$this->_valid = !(next($this->_filters) === false && key($this->_filters) === null);
		$next = current($this->_filters);
		if (empty($self) || empty($chain)) {
			return $next;
		}
		return call_user_func($next, $self, $params, $chain);
	}

	public function append($value) {
		is_object($value) ? $this->_filters[] =& $value : $this->_filters[] = $value;
	}

	public function count() {
		$count = iterator_count($this);
		$this->rewind();
		return $count;
	}

	public function keys() {
		return array_keys($this->_filters);
	}
}

?>