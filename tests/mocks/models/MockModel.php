<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\models;

class MockModel extends \lithium\data\Model {

	protected $_meta = array('connection' => 'mock-source');

	public static function test(){
		$method = __METHOD__;
		return static::_filter(__FUNCTION__, array(), function($self, $params) use ($method) {
			$params[] = $method;
			return $params;
		});
	}

	public static function resetFilters() {
		$class = get_called_class();
		static::$_methodFilters[$class] = array();
	}

	public static function applyFilter($method, $filter = null) {
		$class = get_called_class();
		foreach ((array) $method as $m) {
			if (!isset(static::$_methodFilters[$class][$m])) {
				static::$_methodFilters[$class][$m] = array();
			}
			static::$_methodFilters[$class][$m][] = $filter;
		}
	}
}

?>