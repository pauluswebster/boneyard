<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\behavior;

class MockBehavior extends \sli_base\util\filters\Behavior {

	public static function testBeforeFilter($class, $params, $settings) {
		$params[] = __METHOD__;
		return $params;
	}

	public static function testAfterFilter($class, $params, $settings) {
		$params[] = __METHOD__;
		return $params;
	}

	public static function testFilter($class, $params, $chain, $settings) {
		$params[] =  __METHOD__;
		$params = $chain->next($class, $params, $chain);
		$params[] = __METHOD__;
		return $params;
	}
}
?>