<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\core;

/**
 * The `Observer` base class.
 */
abstract class Observer extends FilterObject {

	/**
	 * Create filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @param array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterMethod($method, $filter, &$settings){
		return static::_filterBeforeMethod($method, $filter, $settings);
	}

	/**
	 * Create before filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @params array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterBeforeMethod($method, $filter, &$settings){
		$class = get_called_class();
		return function($self, $params, $chain) use ($method, $class, $filter, &$settings) {
			$check = array($method .'.before', $method);
			$methods = $settings['methods'];
			if (in_array($check[0], $methods) || in_array($check[1], $methods)) {
				$class::$filter($self, $params, $settings);
			}
			return $chain->next($self, $params, $chain);
		};
	}

	/**
	 * Create after filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @params array $settings filter settings
	 * @return filter closure to apply to class
	 */
	protected static function _filterAfterMethod($method, $filter, &$settings){
		$class = get_called_class();
		return function($self, $params, $chain) use ($method, $class, $filter, &$settings) {
			$params = $chain->next($self, $params, $chain);
			if (in_array($method .'.after', $settings['methods'])) {
				$class::$filter($self, $params, $settings);
			}
			return $params;
		};
	}
}
?>