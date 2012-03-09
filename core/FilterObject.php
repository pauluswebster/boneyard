<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\core;

use lithium\util\collection\Filters;

/**
 * The `FilterObject` base class.
 */
abstract class FilterObject extends \lithium\core\StaticObject {

	/**
	 * Default settings
	 *
	 * @var array
	 */
	protected static $_defaults = array(
		'methods' => array(),
		'apply' => true
	);

	/**
	 * Config
	 *
	 * @var array
	 */
	protected static $_settings = array();

	/**
	 * Cached storage of FilterObject filter methods
	 */
	protected static $_filterMethods = array();


	/**
	 * Apply the filter object to a class
	 */
	public static function &apply($class, array $settings = array()) {
		$settings += static::$_defaults;
		if ($settings['apply']) {
			static::_apply($class, $settings);
		}
		if (is_array($settings['methods'])) {
			static::_applyFilterMethods($class, $settings);
		}
		return $settings;
	}


	/**
	 * Configure binding, redeclare in subclasses as required
	 * settings prior to applying filters.
	 *
	 * @param string $class class object is being applied to
	 * @param array $settings settings for the binding
	 */
	protected static function _apply($class, &$settings) {
		$settings += static::$_settings;
	}

	/**
	 * Apply filter object filter methods to class filters
	 *
	 * @param mixed $class class name or instance
	 * @param array $settings configuration for binding
	 * @return null
	 */
	protected static function _applyFilterMethods($class, &$settings) {
		$filterClass = get_called_class();
		$all = empty($settings['methods']);
		$filterMethods = static::_filterMethods();
		foreach ($filterMethods as $filterMethod) {
			$pattern = '/(.*)(Before|After)Filter$/';
			if (preg_match($pattern, $filterMethod, $matches)) {
				$method = $matches[1];
				$apply = $matches[2];
				$applyMethod = "_filter{$apply}Method";
				if ($all) {
					$settings['methods'][] = $method . '.' . lcfirst($apply);
				}
			} else {
				$applyMethod = '_filterMethod';
				$method = preg_replace('/(.*)Filter$/', '$1', $filterMethod);
				if ($all) {
					$settings['methods'][] = $method;
				}
			}
			$args = array($method, $filterMethod, &$settings);
			$filter = $filterClass::invokeMethod($applyMethod, $args);
			if (!$filter) {
				continue;
			}
			if (is_object($class) || class_exists($class, false)) {
				call_user_func(array($class, 'applyFilter'), $method, $filter);
			} else {
				Filters::apply($class, $method, $filter);
			}
		}
	}

	/**
	 * Get method names of a filter class that will be applied to a class
	 *
	 * @return array methods of class that match the standard filter pattern
	 */
	protected static function _filterMethods() {
		$filterClass = get_called_class();
		if (!isset(static::$_filterMethods[$filterClass])) {
			$methods = get_class_methods($filterClass);
			$pattern = '/(.*)(?<!^apply)Filter$/';
			static::$_filterMethods[$filterClass] = preg_grep($pattern, $methods);
		}
		return static::$_filterMethods[$filterClass];
	}


	/**
	 * Create filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @param array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterMethod($method, $filter, &$settings){}

	/**
	 * Create before filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @params array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterBeforeMethod($method, $filter, &$settings){}

	/**
	 * Create after filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @params array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterAfterMethod($method, $filter, &$settings){}
}

?>