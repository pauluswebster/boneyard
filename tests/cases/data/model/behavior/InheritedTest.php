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
		
		$settings = Inherited::apply(static::$models[1]);
		//first level model
		$expected = static::$models[0];
		$result = $settings['base'];
		$this->assertEqual($expected, $result);
		
		$expected = array(static::$models[0]);
		$result = array_keys($settings['parents']);
		$this->assertEqual($expected, $result);
		
		$settings = Inherited::apply(static::$models[2]);
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
		
		$settings = Inherited::apply(static::$models[1]);
		
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
		array_map(function($model) {
			Inherited::apply($model);
		}, static::$models);
		
		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];
		
		$expected = $model::schema();
		$result = Inherited::schema($model);
		$this->assertEqual($expected, $result);
		
		$expected = $model::schema() + $modelOne::schema();
		$result = Inherited::schema($modelOne);
		$this->assertEqual($expected, $result);
		
		$expected = $model::schema() + $modelOne::schema() + $modelTwo::schema();
		$result = Inherited::schema($modelTwo);
		$this->assertEqual($expected, $result);
	}
	
	/**
	 * @todo - mods to creation coming, current tests match the existing
	 * mapping which is slightly incorrect
	 */
	public function testCreate() {
		array_map(function($model) {
			Inherited::apply($model);
		}, static::$models);
		
		$model = static::$models[0];
		$modelOne = static::$models[1];
		$modelTwo = static::$models[2];
		
		$record = $model::create(array('title' => 'An Item'));
		
		$expected = $record->schema();
		$result = Inherited::schema($model);
		$this->assertEqual($expected, $result);
		
		$expected = array(
			'title' => 'An Item',
			'class' => basename(str_replace('\\', '/', $model))
		);
		$result = $record->data();
		$this->assertEqual($expected, $result);
		
		$record = $modelOne::create(array('title' => 'An Item', 'flavour' => 'Chicken'));
		
		$expected = $record->schema();
		$result = Inherited::schema($modelOne);
		$this->assertEqual($expected, $result);
		
		$data = array(
			'title' => 'An Item',
			'flavour' => 'Chicken',
			'class' => basename(str_replace('\\', '/', $modelOne))
		);
		$expected = array(
			'inherited_mock_item' => array_merge($data, array(
				'class' => basename(str_replace('\\', '/', $model))
			))
		) + $data;
		$result = $record->data();
		$this->assertEqual($expected, $result);
		
		
		$record = $modelTwo::create(array('title' => 'An Item', 'flavour' => 'Beef', 'color' => 'Red'));
		
		$expected = $record->schema();
		$result = Inherited::schema($modelTwo);
		$this->assertEqual($expected, $result);
		
		$data = array(
			'title' => 'An Item',
			'flavour' => 'Beef',
			'color' => 'Red',
			'class' => basename(str_replace('\\', '/', $modelTwo))
		);
		$expected = array(
			'inherited_mock_item_one' => array_merge($data, array(
				'class' => basename(str_replace('\\', '/', $modelOne))
			)),
			'inherited_mock_item' => array_merge($data, array(
				'class' => basename(str_replace('\\', '/', $model))
			))
		) + $data;
		$result = $record->data();
		$this->assertEqual($expected, $result);
	}
	
	public function testValidate() {
		
	}
	
	public function testSave() {
		
	}
	
	public function testFind() {
		
	}
	
	public function testDelete() {
		
	}
}

?>