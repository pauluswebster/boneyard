<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\util\filters;

use lithium\core\Libraries;
use lithium\util\collection\Filters;
use lithium\core\ConfigException;

abstract class FilterObjects extends \lithium\core\StaticObject {

	/**
	 * Namespace => filter object path map
	 *
	 * @see sli_base\core\FilterObjects:paths()
	 * @var array
	 */
	protected static $_paths = array(
		'models' => 'data.model'
	);

	protected static $_path = '';

	/**
	 * Apply filter objects to a class
	 *
	 * @param mixed $class class name or instance
	 * @param mixed $filterClass string class name or array of class names & settings
	 * @param array $settings configuration for binding
	 * @return array $settings indexed by fully namespaced filter object class name
	 */
	public static function apply($class, $filterClass, array $settings = array()) {
		$filterClasses = static::_nomarlizeFilterList($filterClass, $settings);
		$applied = array();
		foreach ($filterClasses as $filter => $settings) {
			$filterClass = static::_class($filter, $class);
			$applied[$filterClass] = $filterClass::apply($class, $settings);
		}
		return $applied;
	}

	/**
	 * Configure mapped namespaces for filter objects
	 *
	 * @param array $paths
	 * @return array configured paths
	 */
	public static function paths(array $paths = array()) {
		if ($paths) {
			static::$_paths = $paths;
		}
		return static::$_paths;
	}

	/**
	 * Locate a bound filter object by base name for bound class
	 *
	 * @param mixed $class class name or instance filterClass is being applied to
	 * @param string $filterClass behavior class name
	 */
	public static function locate($class, $filterClass) {
		return static::_class($filterClass, $class);
	}

	/**
	 * Locate filter class
	 *
	 * @param string $filterClass behavior class name
	 * @param mixed $class class name or instance filterClass is being applied to
	 * @return string fully namespaced behavior class
	 * @throws lithium\core\ConfigException
	 */
	protected static function _class($filterClass, $class) {
		if(strpos($filterClass, '\\') !== false) {
			return $filterClass;
		}
		if (is_object($class)) {
			$class = get_class($class);
		}
		$path = static::_path($class);
		if (!$path || !$_filterClass = static::_locate($path, $filterClass)) {
			$self = get_called_class();
			$exception = "Could not find `{$filterClass}` in class `{$self}`.";
			throw new ConfigException($exception);
		}
		return $_filterClass;
	}

	/**
	 * Obtain a Libraries::locate compatable path for a filter object.
	 *
	 * @params string $class class name that filter is being applied to
	 * @return dot delimited string Libraries::locate() compatable path
	 */
	protected static function _path($class) {
		$classPath = explode('\\', $class);
		$library = array_shift($classPath);
		$class = array_pop($classPath);
		$match = implode('\\', $classPath);
		if (isset(static::$_paths[$match])) {
			$path = static::$_path . '.' . static::$_paths[$match];
		} elseif (!empty($classPath)) {
			$path = implode('.', array_merge(array(static::$_path), $classPath));
		}
		return $path;
	}

	/**
	 * Locate a filter object class in array of paths
	 *
	 * @see lithium\core\Libraries::locate()
	 * @param unknown_type $paths
	 * @param unknown_type $name
	 * @return \lithium\core\mixed
	 */
	protected static function _locate($paths, $name) {
		foreach ((array) $paths as $path) {
			if ($class = Libraries::locate($path, $name)) {
				return $class;
			}
		}
	}

	/**
	 * Format filter object list
	 *
	 * @param mixed $filterClasss
	 * @param array $settings
	 * @return array of filter object settings indexed by name
	 */
	protected static function _nomarlizeFilterList($filterClass, $settings) {
		$filterClasses = array();
		if (!is_array($filterClass)) {
			$filterClasses[$filterClass] = $settings;
		} else {
			$format = function($filterClass, $settings) use(&$filterClasses) {
				if (is_int($filterClass)) {
					$filterClass = $settings;
					$settings = array();
				}
				$filterClasses[$filterClass] = $settings;
			};
			array_map($format, array_keys($filterClass), $filterClass);
		}
		return $filterClasses;
	}
}

?>