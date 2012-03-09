<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\controllers;

class MockController extends \lithium\action\Controller {

	public function test(){
		$method = __METHOD__;
		return $this->_filter(__METHOD__, array(), function($self, $params) use($method) {
			$params[] = $method;
			return $params;
		});
	}

}