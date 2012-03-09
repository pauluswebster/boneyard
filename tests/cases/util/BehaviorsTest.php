<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\util;

use lithium\core\Libraries;
use lithium\data\Connections;
use sli_base\util\Behaviors;
use sli_base\tests\mocks\behavior\MockBehavior;
use sli_base\tests\mocks\models\MockModel;
use sli_base\tests\mocks\controllers\MockController;

class BehaviorsTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockModel';

	protected static $controller = 'sli_base\tests\mocks\controllers\MockController';

	protected static $behavior = 'sli_base\tests\mocks\behavior\MockBehavior';

	public function _init() {
		Libraries::paths(array(
			'behavior' => array_merge(Libraries::paths('behavior'), array(
				'{:library}\tests\mocks\behavior\{:name}'	
			))
		));
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
	}

	public function testApplyStatic() {
		$behavior = static::$behavior;
		$model = static::$model;
		$applied = Behaviors::apply($model, array(
			'MockBehavior' => array('somevar' => true)
		));

		$this->assertTrue(!empty($applied));
		$this->assertTrue(isset($applied[$behavior]));

		$applied =& $applied[$behavior];

		$result = $applied['methods'];
		$expected = array ('test.before', 'test.after', 'test');
		$this->assertEqual($expected, $result);

		$this->assertTrue(isset($applied['somevar']));
		$result = $applied['somevar'];
		$expected = true;
		$this->assertEqual($expected, $result);

		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$behavior}::testFilter",
			"{$model}::test",
			"{$behavior}::testFilter",
			"{$behavior}::testAfterFilter"
		);
		$result = $model::test();
		$this->assertEqual($expected, $result);
	}

	public function testApplyInstance() {
		$behavior = static::$behavior;
		$controller = new static::$controller;
		$controllerClass = static::$controller;
		$applied = Behaviors::apply($controller, array(
			'MockBehavior' => array('somevar' => true)
		));

		$this->assertTrue(!empty($applied));
		$this->assertTrue(isset($applied[$behavior]));

		$applied =& $applied[$behavior];

		$this->assertTrue(isset($applied['somevar']));
		$result = $applied['somevar'];
		$expected = true;
		$this->assertEqual($expected, $result);

		$result = $applied['methods'];
		$expected = array ('test.before', 'test.after', 'test');
		$this->assertEqual($expected, $result);

		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$behavior}::testFilter",
			"{$controllerClass}::test",
			"{$behavior}::testFilter",
			"{$behavior}::testAfterFilter"
		);
		$result = $controller->test();
		$this->assertEqual($expected, $result);
	}

	public function testInstanceSettings() {
		$controller = new static::$controller;
		$behavior = static::$behavior;

		$applied = Behaviors::apply($controller, array(
			'MockBehavior' => array('somevar' => true)
		));

		$this->assertTrue(!empty($applied));
		$this->assertTrue(isset($applied[$behavior]));

		$applied =& $applied[$behavior];

		$this->assertTrue(isset($applied['somevar']));
		$result = $applied['somevar'];
		$expected = true;
		$this->assertEqual($expected, $result);

		$controller2 = new static::$controller;
		$applied2 = Behaviors::apply($controller2, array(
			'MockBehavior' => array('somevar' => false)
		));

		$this->assertTrue(!empty($applied2));
		$this->assertTrue(isset($applied2[$behavior]));

		$applied2 =& $applied2[$behavior];

		$this->assertTrue(isset($applied2['somevar']));
		$result = $applied2['somevar'];
		$expected = false;
		$this->assertEqual($expected, $result);

		$result = $applied['somevar'];
		$expected = true;
		$this->assertEqual($expected, $result);
	}

	public function testOmitStatic() {
		$behavior = static::$behavior;
		$model = static::$model;
		$applied = Behaviors::apply($model, 'MockBehavior', array(
			'methods' => array('test.before', 'test.after')
		));

		$applied =& $applied[$behavior];

		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$model}::test",
			"{$behavior}::testAfterFilter"
		);
		$result = $model::test();
		$this->assertEqual($expected, $result);

		$applied['methods'] = array('test.before', 'test.after', 'test');

		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$behavior}::testFilter",
			"{$model}::test",
			"{$behavior}::testFilter",
			"{$behavior}::testAfterFilter"
		);
		$result = $model::test();
		$this->assertEqual($expected, $result);

		$applied['methods'] = array('test');

		$expected = array (
			"{$behavior}::testFilter",
			"{$model}::test",
			"{$behavior}::testFilter"
		);
		$result = $model::test();
		$this->assertEqual($expected, $result);
	}

	public function testOmitInstance() {
		$controller = new static::$controller;
		$controllerClass = static::$controller;
		$behavior = static::$behavior;

		$applied = Behaviors::apply($controller, array(
			'MockBehavior' => array(
				'methods' => array('test.before', 'test.after')
			)
		));
		$applied =& $applied[$behavior];

		$controller2 = new static::$controller;
		$applied2 = Behaviors::apply($controller2, array(
			'MockBehavior' => array(
				'methods' => array('test')
			)
		));
		$applied2 =& $applied2[$behavior];

		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$controllerClass}::test",
			"{$behavior}::testAfterFilter"
		);
		$result = $controller->test();
		$this->assertEqual($expected, $result);

		$expected = array (
			"{$behavior}::testFilter",
			"{$controllerClass}::test",
			"{$behavior}::testFilter"
		);
		$result = $controller2->test();
		$this->assertEqual($expected, $result);

		$applied2['methods'] = array('test.before', 'test.after');
		$expected = array (
			"{$behavior}::testBeforeFilter",
			"{$controllerClass}::test",
			"{$behavior}::testAfterFilter"
		);
		$result = $controller->test();
		$this->assertEqual($expected, $result);
	}
}

?>