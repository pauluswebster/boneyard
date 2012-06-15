<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\util;

use lithium\util\Set;

/**
 * The `Store` class provides low level handling of array access utilizing
 * dot-delimeted strings as array paths.
 */
class Store {

	/**
	 * Takes a dot-delimited string or array of dot-delimited strings and uses
	 * them to create an array structure.
	 *
	 * This method can be used to resurrect arrays flattened with
	 * Set::flatten() Also see Store::unflatten(),
	 * Store::flatten() for further usage with data at set paths.
	 *
	 * Basic Examples:
	 *
	 * {{{
	 * $array = Store::create('foo.bar');
	 * //equivelent
	 * $array = array(
	 * 	'foo' => array(
	 * 		'bar' => null
	 * 	)
	 * );
	 *
	 * $array = Store::create(array('user.name','user.email'));
	 * //equivelent
	 * $array = array(
	 * 	'user' => array(
	 * 		'name' => null,
	 * 		'email' => null
	 * 	)
	 * );
	 *
	 * //assign values too
	 * $array = Store::create(array('user.postal.details' => array('name' => 'Paul')));
	 * //equivelent
	 * $array = array(
	 * 	'user' => array(
	 * 		'postal' => array(
	 * 			'details' => array(
	 * 				'name' => 'Paul',
	 * 			)
	 * 		)
	 * 	)
	 * );
	 *
	 * }}}
	 */
	public static function create($path, $default = null){
		$array = array();
		if (!isset($path)) {
			return $array;
		}
		if (!is_array($path)) {
			$path = array($path);
		}
		foreach ($path as $key => $value) {
			$_path = (is_int($key) && isset($value)) ? $value : $key;
			$_value = ($_path !== $key) ? $default : $value;
			if (strpos($_path, '.') !== false) {
				$_path = explode('.', $_path);
			} else {
				$_path = array($_path);
			}
			$depth = $count = count($_path);
			$create =& $array;
			while ($depth) {
				$_key = $_path[$count - $depth];
				$depth--;
				if ($depth) {
					if (!isset($create[$_key])) {
						$create[$_key] = array();
					}
					$create =& $create[$_key];
				} else {
					$create[$_key] = $_value;
				}
			}
		}
		return $array;
	}

	/**
	 * Write values to array
	 *
	 * @param array $array
	 * @param mixed $path string path to write | array paths & values to write
	 * @param mixed $value array value to write to $path if it is string path
	 * @param boolean $merge true merge pased in $value with $array
	 * @return array
	 */
	public static function set($array, $path, $value = null, $merge = false) {
		if ($merge) {
			return static::merge($array, $path, $value);
		}
		if ($path = static::_formatWritePath($path, $value)) {
			foreach ($path as $_path => $value) {
				if (strpos($_path, '.') === false) {
					$array[$_path] = $value;
				} else {
					$p = explode('.', $_path, 2);
					if (!array_key_exists($p[0], $array)) {
						$array[$p[0]] = array();
					}
					$array[$p[0]] = Set::insert($array[$p[0]], $p[1], $value);
				}
			}
		}
		return $array;
	}

	/**
	 * Write values to the array by merging with current values at the specified path
	 *
	 * @param array $array
	 * @param mixed $path
	 * @param mixed $value null | value to merge with the config located at $path
	 * @return array
	 */
	public static function merge(array $array, $path, $value = null){
		if ($path = static::_formatWritePath($path, $value)) {
			foreach ($path as $_path => $value) {
				$current = static::get($array, $_path);
				if (is_array($current) && is_array($value)) {
					$value = Set::merge($current, $value);
				}
				$array = static::set($array, $_path, $value);
			}
		}
		return $array;
	}

	/**
	 * Read a value from the array
	 *
	 * @param array $array
	 * @param mixed $path dot-delimited string path | array paths
	 * @return mixed
	 */
	public static function get(array $array, $path = null) {
		if (!isset($path)) {
			return $array;
		}
		if (is_array($path)) {
			foreach ($path as $_path) {
				$value[$_path] = static::get($array, $_path);
			}
		} else {
			if (strpos($path, '.') !== false) {
				$path = explode('.', $path);
			} else {
				$path = array($path);
			}
			if (!array_key_exists($path[0], $array)) {
				return;
			}
			if (count($path) === 1) {
				return $array[$path[0]];
			}
			$value = $array[$path[0]];
			$count = count($path);
			$index = 0;
			while(++$index < $count) {
				if (is_array($value) && array_key_exists($path[$index], $value)) {
					$value = $value[$path[$index]];
					continue;
				}
				return;
			}
		}
		return $value;
	}

	/**
	 * Check if array key exists
	 *
	 * @param array $array
	 * @param string $path
	 * @return boolean
	 */
	public static function keyExists(array $array, $path) {
		if (strpos($path, '.') === false) {
			return array_key_exists($path, $array);
		}
		$path = explode('.', $path);
		if (!array_key_exists($path[0], $array)) {
			return false;
		}
		$count = count($path);
		$index = -1;
		while(++$index < $count) {
			if (is_array($array) && array_key_exists($path[$index], $array)) {
				$array = $array[$path[$index]];
				continue;
			}
			return false;
		}
		return true;
	}

	/**
	 * Filter to array path
	 *
	 * @param array $array
	 * @param string $path
	 * @param mixed $filter callable
	 */
	public static function filter(array $array, $path, $filter) {
		$data = static::get($array, $path);
		if (is_array($data)) {
			return array_filter($filter, $data);
		}
	}

	/**
	 * Apply filter callback to array path
	 *
	 * @param array $array
	 * @param string $path
	 * @param mixed $filter callable
	 */
	public static function apply(array $array, $path, $filter) {
		$data = static::get($array, $path);
		if (is_array($data)) {
			return array_map($filter, $data);
		}
	}

	/**
	 * Extract values from the array
	 * Similar to get except the array structure is preserved in the result
	 *
	 * @param array $array
	 * @param mixed $path
	 */
	public static function extract(array $array, $path = null){
		if (!isset($path)) {
			return $array;
		}
		if (is_array($path)) {
			$extracted = static::create($path);
			foreach ($path as $_path) {
				$value = static::get($array, $_path);
				$extracted = static::set($extracted, $_path, $value);
			}
		} else {
			$value = static::get($array, $path);
			$extracted = static::create($path);
			$extracted = static::set($extracted, $path, $value);
		}
		return $extracted;
	}

	/**
	 * Remove an array value by path
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return array
	 */
	public static function delete(array $array, $path = null) {
		if (!isset($path)) {
			$array = array();
		} elseif(is_array($path)) {
			foreach ($path as $_path) {
				$array = static::delete($array, $_path);
			}
		} elseif (strpos($path, '.') === false) {
			unset($array[$path]);
		} else {
			$p = explode('.', $path, 2);
			if ($p && array_key_exists($p[0], $array)) {
				$array[$p[0]] = Set::remove($array[$p[0]], $p[1]);
			}
		}
		return $array;
	}

	/**
	 * Get keys at array path
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return mixed
	 */
	public static function keys(array $array, $path = null){
		if (!isset($path)) {
			$keys = array_keys($array);
		} else {
			$value = static::get($array, $path);
			$keys = $value && is_array($value) ? array_keys($value) : array();
		}
		return $keys;
	}

	/**
	 * Pass array value from path through to Set::flatten()
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return array
	 */
	public static function flatten(array $array, $path = null){
		$array = static::get($array, $path);
		return Set::flatten($array);
	}
	
	/**
	 * Expand flattened array via Set::extract(), optionally get value from path only
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return mixed
	 */
	public static function expand(array $array, $path = null) {
		$array = Set::expand($array);
		return static::get($array, $path);
	}

	/**
	 * Resurrect a flattened array, optionally etxract from path
	 *
	 * @param array $array
	 * @param mixed $path
	 * @return mixed
	 */
	public static function unflatten(array $array, $path = null){
		$array = static::create($array);
		return static::get($array, $path);
	}

	/**
	 * Formats write path & value as an array for set & merge methods
	 *
	 * @param mixed $path
	 * @param mixed $value
	 * @return mixed
	 */
	protected static function _formatWritePath($path, $value){
		if (!is_array($path)) {
			if (!isset($path)) {
				if (!isset($value)) {
					return false;
				}
				$path = (array) $value;
			} else {
				$path = array($path => $value);
			}
		}
		return $path;
	}
}

?>