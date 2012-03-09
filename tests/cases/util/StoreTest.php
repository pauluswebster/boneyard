<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\util;

use sli_base\util\Store;

class StoreTest extends \lithium\test\Unit {

	public function testCreate(){
		$expected = array();
		$result = Store::create(null);
		$this->assertIdentical($expected, $result);

		$expected = array('user' => 'me');
		$result = Store::create('user', 'me');
		$this->assertIdentical($expected, $result);

		$expected = array(
			'my' => array(
				'new' => array(
					'array' => null
				),
				'loves' => array(
					'arrays' => null
				)
			)
		);
		$result = Store::create(array('my.new.array', 'my.loves.arrays'));
		$this->assertIdentical($expected, $result);
		$expected['my']['new']['array'] = true;
		$result = Store::create(array('my.new.array' => true, 'my.loves.arrays'));
		$this->assertIdentical($expected, $result);

		$result = Store::create(Store::flatten($expected));
		$this->assertIdentical($expected, $result);

		$result = Store::unflatten(Store::flatten($expected));
		$this->assertIdentical($expected, $result);

		$expected = array(7 => true, 0 => true);
		$result = Store::create(array(7, 0), true);
		$this->assertIdentical($expected, $result);
		$expected = array_fill_keys(array(7, 0), true);
		$this->assertIdentical($expected, $result);
	}

	public function testSingleKey(){
		$source = array(
			'Farm',
			'Foo' => 'Bar',
			'Cows' => 'Moo',
			'Pig' => 'Oink',
			7 => 'Seven'
		);
		$expected = $source + array(8 => 'Eight');
		$result = Store::set($source, 8, 'Eight');
		$this->assertIdentical($expected, $result);
		$result = Store::set($source, array(8 => 'Eight'));
		$this->assertIdentical($expected, $result);
		$result = Store::set($source, null, array(8 => 'Eight'));
		$this->assertIdentical($expected, $result);
		$this->assertTrue(Store::keyExists($result, 8));

		$expected = $source;
		$expected[0] = 'House';
		$result = Store::set($source, 0, 'House');
		$this->assertIdentical($expected, $result);
		$new = array('Duck' => 'Quack', 'Dog' => 'Woof');
		$expected = $source + $new;
		$result = Store::set($source, $new);
		$this->assertIdentical($expected, $result);

		$expected = $source;
		$result = Store::set($source, null);
		$this->assertIdentical($expected, $result);

		$this->assertTrue(Store::keyExists($source, 0));
		$expected = 'Farm';
		$result = Store::get($source, 0);
		$this->assertIdentical($expected, $result);

		$expected = 0;
		$result = Store::set($source, 0, 0);
		$result = Store::get($result, 0);
		$this->assertIdentical($expected, $result);

		$result = Store::get($source, '0.0');
		$this->assertNull($result);

		$this->expectException();
		$result = Store::get($source, 0.0);
		$this->assertNull($result);

		$expected = $source;
		$expected['Duck'] = 'Quack';
		$expected['Cow'] = 'MOO!!';
		$result = Store::merge($source, array('Duck' => 'Quack', 'Cow' => 'MOO!!'));
		$this->assertIdentical($expected, $result);

		$expected = array(
			7 => 'Seven'
		);
		$result = Store::extract($expected, 7);
		$this->assertIdentical($expected, $result);
		$result = Store::get($expected, array(7));
		$this->assertIdentical($expected, $result);

		$expected = array(
			'Foo' => 'Bar',
			'Cows' => 'Moo'
		);
		$result = Store::extract($expected, array('Foo', 'Cows'));
		$this->assertIdentical($expected, $result);

		$expected = $source;
		$result = Store::extract($source, null);
		$this->assertIdentical($expected, $result);

		$expected = array_keys($source);
		$result = Store::keys($source);
		$this->assertIdentical($expected, $result);

		$expected = array();
		$result = Store::delete($source);
		$expected = $source;
		unset($expected['Pig']);
		$result = Store::delete($source, 'Pig');
	}

	function testNestedKeys(){
		$source = array(
			'app' => array(
				'name' => 'Slicedup',
				'config' => array(
					'x' => array(
						9 => true,
						1 => array(
							'foo',
							'bar'
						)
					)
				),
			),
			0 => null,
			2 => array(
				1,
				1 => 2
			)
		);
		$expected = $source;
		$flat = Store::flatten($expected);
		$result = Store::unflatten($flat);
		$this->assertIdentical($expected, $result);

		$expected = 'Slicedup';
		$this->assertTrue(Store::keyExists($source, 'app'));
		$this->assertTrue(Store::keyExists($source, 0));
		$this->assertTrue(Store::keyExists($source, 'app.name'));
		$this->assertTrue(Store::keyExists($source, 2.1));
		$this->assertFalse(Store::keyExists($source, 'name'));
		$this->assertTrue(Store::keyExists($source, 'app.config.x.9'));
		$this->assertTrue(Store::keyExists($source, 'app.config.x.1.1'));
		$result = Store::get($source, 'app.name');
		$this->assertIdentical($expected, $result);

		$expected = array(
			'app.name' => 'Slicedup'
		);
		$result = Store::get($source, array('app.name'));
		$this->assertIdentical($expected, $result);

		$expected = array(
			'app.name' => 'Slicedup',
			'2.1' => 2
		);
		$result = Store::get($source, array('app.name', '2.1'));
		$this->assertIdentical($expected, $result);

		$expected = $source;
		$expected['app']['name'] = 'slicedup';
		$result = Store::set($source, 'app.name', 'slicedup');
		$this->assertIdentical($expected, $result);
		$result = Store::set($source, array('app.name' => 'slicedup'));
		$this->assertIdentical($expected, $result);
		$result = Store::set($source, null, array('app.name' => 'slicedup'));
		$this->assertIdentical($expected, $result);

		$expected[2][1] = false;
		$result = Store::set($source, array('app.name' => 'slicedup', '2.1' => false));
		$this->assertIdentical($expected, $result);

		$expected = $source;
		$expected[3][3] = true;
		$result = Store::set($source, '3.3', true);
		$this->assertIdentical($expected, $result);

		$expected = $source;
		$expected[3][3] = true;
		$result = Store::merge($source, '3.3', true);
		$this->assertIdentical($expected, $result);
		$expected['app'] = null;
		$result = Store::merge($source, array('app' => null, '3.3' => true));
		$this->assertIdentical($expected, $result);

		$expected = array('name', 'config');
		$result = Store::keys($source, 'app');
		$this->assertIdentical($expected, $result);

		$expected = array(9, 1);
		$result = Store::keys($source, 'app.config.x');
		$this->assertIdentical($expected, $result);

		$expected = $source;
		unset($expected['app']['name']);
		$result = Store::delete($source, 'app.name');

		$expected = $source;
		unset($expected[2][1]);
		$result = Store::delete($source, '2.1');

		$expected = array('app' => array('name' => 'Slicedup'));
		$result = Store::extract($source, array('app.name'));
		$this->assertIdentical($expected, $result);
		$result = Store::extract($source, 'app.name');
		$this->assertIdentical($expected, $result);

		$expected = array('app' => array('name' => 'Slicedup', 'config' => array('x' => array(9 => true))));
		$result = Store::extract($source, array('app.name', 'app.config.x.9'));
		$this->assertIdentical($expected, $result);
	}
}

?>