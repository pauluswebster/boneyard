<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\util\filters;

use lithium\data\Connections;
use sli_base\util\filters\Events;

class EventsTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPost';

	protected static $controller = 'sli_base\tests\mocks\controllers\MockController';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
	}

	public function testAttach() {
		$model = static::$model;

		$run = array();
		$observer = function($class, $params, $key) use(&$run) {
			$run[] = $key;
		};

		$before = Events::before($model, 'create', $observer);

		$model::create();
		$this->assertTrue(in_array($before, $run));

		$run = array();
		$after = Events::after($model, 'create', $observer);

		$model::create();
		$this->assertTrue(in_array($before, $run));
		$this->assertTrue(in_array($after, $run));
	}

	public function testAddRemove() {
		$model = static::$model;

		$run = array();
		$observer = function($class, $params, $key) use(&$run) {
			$run[] = $key;
		};

		$before = Events::before($model, 'create', $observer);
		$this->assertTrue(Events::applied($before));

		$model::create();
		$this->assertTrue(in_array($before, $run));

		$run = array();
		Events::remove($before);
		$this->assertFalse(Events::applied($before));

		$model::create();
		$this->assertFalse(in_array($before, $run));
	}

	public function testSingleObservation() {
		$model = static::$model;

		$run = array();
		$observer = function($class, $params, $key) use(&$run) {
			$run[] = $key;
		};

		$before = Events::before($model, 'create', $observer, false);
		$this->assertTrue(Events::applied($before));

		$model::create();
		$this->assertTrue(in_array($before, $run));
		$this->assertFalse(Events::applied($before));

		$run = array();
		$model::create();
		$this->assertFalse(in_array($before, $run));
	}

}

?>