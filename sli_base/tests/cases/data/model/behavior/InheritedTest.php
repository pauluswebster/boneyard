<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2012, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\data\model\behavior;

use lithium\data\Connections;
use sli_base\data\model\behavior\Inherited;

class InheritedTest extends \lithium\test\Unit {

	protected static $models = array(
		'sli_base\tests\mocks\models\MockItem',
		'sli_base\tests\mocks\models\MockItemOne',
		'sli_base\tests\mocks\models\MockItemTwo'
	);

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		foreach (static::$models as $model) {
			$model::resetFilters();
			$model::resetRelations();
		}
		Connections::get('mock-source')->flush();
	}

	/**
	 * Test model base and inheritance detection
	 */
	public function testParents() {
		$settings = Inherited::apply(static::$models[0]);
		//base level model
		$expected = static::$models[0];
		$result = $settings['base'];
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $settings['parents'];
		$this->assertEqual($expected, $result);

		$settings = Inherited::apply(static::$models[1], array('base' => static::$models[0]));
		//first level model
		$expected = static::$models[0];
		$result = $settings['base'];
		$this->assertEqual($expected, $result);

		$expected = array(static::$models[0]);
		$result = array_keys($settings['parents']);
		$this->assertEqual($expected, $result);

		$settings = Inherited::apply(static::$models[2], array('base' => static::$models[0]));
		//nth level model
		$expected = static::$models[0];
		$result = $settings['base'];
		$this->assertEqual($expected, $result);

		$expected = array(static::$models[1], static::$models[0]);
		$result = array_keys($settings['parents']);
		$this->assertEqual($expected, $result);

		$settings = Inherited::apply(static::$models[2], array('base' => static::$models[1]));
		//nth level model, with reduced inheritance
		$expected = static::$models[1];
		$result = $settings['base'];
		$this->assertEqual($expected, $result);

		$expected = array(static::$models[1]);
		$result = array_keys($settings['parents']);
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test binding settings
	 */
	public function testBinding() {
		$model = static::$models[0];
		$relations = $model::relations();

		Inherited::apply(static::$models[0]);

		$expected = $relations;
		$result = $model::relations();
		$this->assertEqual($expected, $result);

		$settings = Inherited::apply(static::$models[1], array('base' => static::$models[0]));

		$expected = array(
			static::$models[0] => array(
				'key' => 'id',
				'to' => static::$models[0],
				'name' => 'InheritedMockItem',
				'fieldName' => 'inherited_mock_item'
			)
		);
		$result = $settings['parents'];
		$this->assertEqual($expected, $result);

		$model = static::$models[1];
		$relation = $model::relations('InheritedMockItem');
		$result = $relation instanceof \lithium\data\model\Relationship;
		$this->assertTrue($result);
		return;

		$config = $relation->data();

		$expected = 'belongsTo';
		$result = $config['type'];
		$this->assertEqual($expected, $result);

		$expected = static::$models[0];
		$result = $config['to'];
		$this->assertEqual($expected, $result);

		$expected = 'inherited_mock_item';
		$result = $config['fieldName'];
		$this->assertEqual($expected, $result);

		$settings = Inherited::apply(static::$models[2], array('prefix' => 'Extended'));

		$expected = array(
			static::$models[1] => array(
				'key' => 'id',
				'to' => static::$models[1],
				'name' => 'ExtendedMockItemOne',
				'fieldName' => 'extended_mock_item_one'
			),
			static::$models[0] => array(
				'key' => 'id',
				'to' => static::$models[0],
				'name' => 'ExtendedMockItem',
				'fieldName' => 'extended_mock_item'
			)
		);
		$result = $settings['parents'];
		$this->assertEqual($expected, $result);

		$model = static::$models[2];

		$relation = $model::relations('ExtendedMockItemOne');
		$result = $relation instanceof \lithium\data\model\Relationship;
		$this->assertTrue($result);

		$relation = $model::relations('ExtendedMockItem');
		$result = $relation instanceof \lithium\data\model\Relationship;
		$this->assertTrue($result);
	}

	public function testSchema() {
		$models = static::$models;
		array_map(function($model) use($models) {
			$config = array();
			if (array_search($model, $models)) {
				$config['base'] = $models[0];
			}
			Inherited::apply($model, $config);
		}, static::$models);

		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];

		$expected = $model::schema();
		$result = Inherited::schema($model);
		$this->assertEqual($expected, $result);

		$expected = $model::schema()->fields() + $modelOne::schema()->fields();
		$result = Inherited::schema($modelOne)->fields();
		$this->assertEqual($expected, $result);

		$expected = $model::schema()->fields() + $modelOne::schema()->fields() + $modelTwo::schema()->fields();
		$result = Inherited::schema($modelTwo)->fields();
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test record creation
	 */
	public function testCreate() {
		$models = static::$models;
		array_map(function($model) use($models) {
			$config = array();
			if (array_search($model, $models)) {
				$config['base'] = $models[0];
			}
			Inherited::apply($model, $config);
		}, static::$models);

		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];

		$record = $model::create(array('title' => 'An Item'));

		$expected = array(
			'title' => 'An Item',
			'class' => basename(str_replace('\\', '/', $model))
		);
		$result = $record->data();
		$this->assertEqual($expected, $result);

		$record = $modelOne::create(array('title' => 'An Item', 'flavour' => 'Chicken'));

		$data = array(
			'title' => 'An Item',
			'flavour' => 'Chicken',
			'class' => basename(str_replace('\\', '/', $modelOne))
		);
		$expected = array(
			'inherited_mock_item' => $data
		) + $data;
		$result = $record->data();
		$this->assertEqual($expected, $result);


		$record = $modelTwo::create(array('title' => 'An Item', 'flavour' => 'Beef', 'color' => 'Red'));

		$data = array(
			'title' => 'An Item',
			'flavour' => 'Beef',
			'color' => 'Red',
			'class' => basename(str_replace('\\', '/', $modelTwo))
		);
		$expected = array(
			'inherited_mock_item_one' => $data,
			'inherited_mock_item' => $data
		) + $data;
		$result = $record->data();
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test record validation
	 * @todo
	 */
	public function testValidate() {}

	/**
	 * Test record persistence
	 */
	public function testSave() {
		$models = static::$models;
		array_map(function($model) use($models) {
			$config = array();
			if (array_search($model, $models)) {
				$config['base'] = $models[0];
			}
			Inherited::apply($model, $config);
		}, static::$models);

		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];
		$connection = $model::connection();

		$record = $model::create(array('title' => 'First Item', 'flavour' => 'Fish', 'color' => 'Blue'));
		$record->save();

		$recordOne = $modelOne::create(array('title' => 'An Item', 'flavour' => 'Beef', 'color' => 'Red'));
		$recordOne->save();

		$recordTwo = $modelTwo::create(array('title' => 'Another Item', 'flavour' => 'Chicken', 'color' => 'Green'));
		$recordTwo->save();

		$expected = array(
			$model => array(
				1 => array('id' => 1, 'title' => 'First Item', 'class' => 'MockItem'),
				2 => array('id' => 2, 'title' => 'An Item', 'class' => 'MockItemOne'),
				3 => array('id' => 3, 'title' => 'Another Item', 'class' => 'MockItemTwo')
			),
			$modelOne => array(
				2 => array('id' => 2, 'flavour' => 'Beef'),
				3 => array('id' => 3, 'flavour' => 'Chicken')
			),
			$modelTwo => array(
				3 => array('id' => 3, 'color' => 'Green')
			)
		);

		$result = $connection->dump();
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test model find
	 */
	public function testFind() {
		$models = static::$models;
		array_map(function($model) use($models) {
			$config = array();
			if (array_search($model, $models)) {
				$config['base'] = $models[0];
			}
			Inherited::apply($model, $config);
		}, static::$models);

		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];
		$connection = $model::connection();

		$rows = array(
			array('title' => 'First Item', 'flavour' => 'Fish', 'color' => 'Blue'),
			array('title' => 'An Item', 'flavour' => 'Beef', 'color' => 'Red'),
			array('title' => 'Another Item', 'flavour' => 'Chicken', 'color' => 'Green')
		);
		$record = $model::create($rows[0]);
		$record->save();
		$recordOne = $modelOne::create($rows[1]);
		$recordOne->save();
		$recordTwo = $modelTwo::create($rows[2]);
		$recordTwo->save();

		$expected = array(
			array (
				'id' => 1,
				'title' => 'First Item',
				'class' => 'MockItem',
			),
			array (
			  'inherited_mock_item' =>
			  array (
			    'id' => 2,
			    'title' => 'An Item',
			    'class' => 'MockItemOne',
			  ),
			  'id' => 2,
			  'flavour' => 'Beef',
			  'title' => 'An Item',
			  'class' => 'MockItemOne',
			),
			array (
			  'inherited_mock_item_one' =>
			  array (
			    'id' => 3,
			    'flavour' => 'Chicken',
			  ),
			  'inherited_mock_item' =>
			  array (
			    'id' => 3,
			    'title' => 'Another Item',
			    'class' => 'MockItemTwo',
			  ),
			  'id' => 3,
			  'color' => 'Green',
			  'flavour' => 'Chicken',
			  'title' => 'Another Item',
			  'class' => 'MockItemTwo',
			)
		);

		$result = $model::first()->data();
		$this->assertEqual($expected[0], $result);
		$result = $model::all()->data();
		$this->assertEqual($expected[0], $result[1]);

		$result = $modelOne::first()->data();
		$this->assertEqual($expected[1], $result);
		$result = $modelOne::all()->data();
		$this->assertEqual($expected[1], $result[2]);

		$result = $modelTwo::first()->data();
		$this->assertEqual($expected[2], $result);
		$result = $modelTwo::all()->data();
		$this->assertEqual($expected[2], $result[3]);
	}

	/**
	 * Test record deletion
	 */
	public function testDelete() {
		$models = static::$models;
		array_map(function($model) use($models) {
			$config = array();
			if (array_search($model, $models)) {
				$config['base'] = $models[0];
			}
			Inherited::apply($model, $config);
		}, static::$models);

		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];
		$connection = $model::connection();

		$rows = array(
			array('title' => 'First Item', 'flavour' => 'Fish', 'color' => 'Blue'),
			array('title' => 'An Item', 'flavour' => 'Beef', 'color' => 'Red'),
			array('title' => 'Another Item', 'flavour' => 'Chicken', 'color' => 'Green')
		);
		$record = $model::create($rows[0]);
		$record->save();
		$recordOne = $modelOne::create($rows[1]);
		$recordOne->save();
		$recordTwo = $modelTwo::create($rows[2]);
		$recordTwo->save();

		$record = $model::first();
		$this->assertTrue($record instanceof \lithium\data\Entity);
		$record->delete();

		$recordOne = $modelOne::first();
		$this->assertTrue($recordOne instanceof \lithium\data\Entity);
		$recordOne->delete();

		$recordTwo = $modelTwo::first();
		$this->assertTrue($recordTwo instanceof \lithium\data\Entity);
		$recordTwo->delete();

		$expected = null;
		$result = $model::first();
		$this->assertEqual($expected, $result);

		$expected = null;
		$result = $modelOne::first();
		$this->assertEqual($expected, $result);

		$expected = null;
		$result = $modelTwo::first();
		$this->assertEqual($expected, $result);
	}
}

?>