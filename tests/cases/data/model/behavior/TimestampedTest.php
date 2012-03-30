<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\data\model\behavior;

use lithium\data\Connections;
use sli_base\core\Behaviors;
use sli_base\data\model\behavior\Timestamped;

class TimestampedTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPage';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
		Connections::get('mock-source')->flush();
	}

	public function testDefaults(){
		$model = static::$model;
		$applied = Timestamped::apply($model);

		$record = $model::create();
		$save = $record->save();
		$data = $model::first()->data();
		$this->assertTrue(isset($data['created']));
		$this->assertFalse(isset($data['updated']));
	}

	public function testCustomFields(){
		$model = static::$model;
		$applied = Timestamped::apply($model, array(
			'create' => 'created',
			'update' => 'modified'
		));

		$record = $model::create();
		$save = $record->save();
		$data = $model::first()->data();
		$this->assertTrue(isset($data['created']));
		$this->assertTrue(isset($data['modified']));
	}

	public function testCustomFormat(){
		$model = static::$model;
		$applied = Timestamped::apply($model, array(
			'update' => 'modified',
			'format' => 'U'
		));

		$record = $model::create();
		$save = $record->save();
		$data = $model::first()->data();
		$this->assertPattern('/\d{10,}/', $data['created']);
		$this->assertPattern('/\d{10,}/', $data['modified']);
	}

	public function testCustomFieldFormat(){
		$model = static::$model;
		$applied = Timestamped::apply($model, array(
			'update' => array(
				'field' => 'modified',
				'format' => 'U'
			)
		));

		$record = $model::create();
		$save = $record->save();
		$data = $model::first()->data();
		$this->assertNoPattern('/\d{10,}/', $data['created']);
		$this->assertPattern('/\d{10,}/', $data['modified']);
	}
}

?>