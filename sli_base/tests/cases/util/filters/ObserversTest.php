<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\util\filters;

use lithium\data\Connections;
use sli_base\util\filters\Observers;

class ObserversTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPost';

	protected static $controller = 'sli_base\tests\mocks\controllers\MockController';

	protected static $observer = '';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
	}

	public function testApplyStatic() {}
}

?>