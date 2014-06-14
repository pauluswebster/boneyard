<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2012, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\data\model\behavior;

use lithium\data\Connections;
use sli_base\data\model\behavior\Tree;

class TreeTest extends \lithium\test\Unit {

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {}
	
	public function testSomething() {}
}

?>