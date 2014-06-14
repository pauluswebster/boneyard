<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\util\filters;

use lithium\util\collection\Filters;

/**
 * The `FilterObject` base class.
 */
abstract class FilterObject extends \lithium\core\StaticObject {

	/**
	 * Default settings for all bindings
	 *
	 * @var array
	 */
	protected static $_defaults = array(
		'methods' => array(),
		'apply' => true
	);

	/**
	 * Config settings, set config defaults for you subclasses here
	 *
	 * @var array
	 */
	protected static $_settings = array();

	/**
	 * Cached storage of FilterObject filter methods
	 */
	protected static $_filterMethods = array();
	
	/**
	 * Internal storage of binding settings.
	 * 
	 * @var array
	 */
	protected static $__settings = array();

	/**
	 * Apply the filter object to a class
	 * 
	 * @param string $class class object is being applied to
	 * @param array $settings settings for the binding
	 * @return array binding settings for $class
	 */
	public static function apply($class, array $settings = array()) {
		static::_settings($class, false);
		$settings += static::$_defaults;
		if ($settings['apply']) {
			$settings = static::_apply($class, $settings);
			$settings = static::_applyFilterMethods($class, $settings);
		}
		return static::settings($class, $settings);
	}

	/**
	 * Get or set settings for the binding
	 * 
	 * @param string $class class object is being applied to
	 * @param array $settings settings for the binding
	 * @return array binding settings for $class
	 */
	public static function settings($class, array $settings = array()) {
		return static::_settings($class, $settings);
	}
	
	/**
	 * Internal handling to get or set settings for binding
	 * 
	 * Note: to prpoerly handle instance based bindings this method could
	 * be overwritten to proxy the storage & access to a property of the
	 * class instance, i.e. by default we only handle binding settings
	 * to single instance of a bound object.
	 * 
 	 * @param string $class class object is being applied to
	 * @param array $settings settings for the binding
	 * @return array binding settings for $class
	 */
	protected static function _settings($class, $settings = array()) {
		$self = get_called_class();
		if (is_object($class)) {
			$class = get_class($class);
		}
		if ($settings === false || !isset(static::$__settings[$self][$class])) {
			static::$__settings[$self][$class] = array();	
		} 
		if (!empty($settings)) {
			static::$__settings[$self][$class] = $settings + static::$__settings[$self][$class];
		}
		return static::$__settings[$self][$class];
	}

	/**
	 * Configure binding, redeclare in subclasses as required
	 * settings prior to applying filters.
	 *
	 * @param string $class class object is being applied to
	 * @param array $settings settings for the binding
	 * @return $settings
	 */
	protected static function _apply($class, $settings) {
		return $settings + static::$_settings;
	}

	/**
	 * Apply filter object filter methods to class filters
	 *
	 * @param mixed $class class name or instance
	 * @param array $settings configuration for binding
	 * @return $settings with `method` key modified as required
	 */
	protected static function _applyFilterMethods($class, $settings) {
		$self = get_called_class();
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
			$args = array($method, $filterMethod);
			$filter = $self::invokeMethod($applyMethod, $args);
			if (!$filter) {
				continue;
			}
			if (is_object($class)) {
				call_user_func(array($class, 'applyFilter'), $method, $filter);
			} else {
				Filters::apply($class, $method, $filter);
			}
		}
		return $settings;
	}

	/**
	 * Get method names of a filter class that will be applied to a class
	 *
	 * @return array methods of class that match the standard filter pattern
	 */
	protected static function _filterMethods() {
		$self = get_called_class();
		if (!isset(static::$_filterMethods[$self])) {
			$methods = get_class_methods($self);
			$pattern = '/(.*)(?<!^apply)Filter$/';
			static::$_filterMethods[$self] = preg_grep($pattern, $methods);
		}
		return static::$_filterMethods[$self];
	}


	/**
	 * Create filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterMethod($method, $filter){}

	/**
	 * Create before filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterBeforeMethod($method, $filter){}

	/**
	 * Create after filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterAfterMethod($method, $filter){}
}

?>