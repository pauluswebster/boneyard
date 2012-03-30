<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\util\filters;

/**
 * The `Behavior` base class.
 */
abstract class Behavior extends FilterObject {

	/**
	 * Create filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterMethod($method, $filter){
		$class = get_called_class();
		return function($self, $params, $chain) use ($method, $class, $filter) {
			$settings = $class::settings($self);
			if (!in_array($method, $settings['methods'])) {
				return $chain->next($self, $params, $chain);
			}
			return $class::$filter($self, $params, $chain, $settings);
		};
	}

	/**
	 * Create before filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterBeforeMethod($method, $filter){
		$class = get_called_class();
		return function($self, $params, $chain) use ($method, $class, $filter) {
			$settings = $class::settings($self);
			if (in_array($method .'.before', $settings['methods'])) {
				$params = $class::$filter($self, $params, $settings);
			}
			return $chain->next($self, $params, $chain, $settings);
		};
	}

	/**
	 * Create after filter closure
	 *
	 * @param string $method class method being filtered
	 * @param string $filter filter method
	 * @return filter closure to apply to class
	 */
	protected static function _filterAfterMethod($method, $filter){
		$class = get_called_class();
		return function($self, $params, $chain) use ($method, $class, $filter) {
			$settings = $class::settings($self);
			$params = $chain->next($self, $params, $chain);
			if (in_array($method .'.after', $settings['methods'])) {
				$params = $class::$filter($self, $params, $settings);
			}
			return $params;
		};
	}
}

?>