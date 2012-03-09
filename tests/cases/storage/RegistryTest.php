<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\storage;

use sli_base\storage\Registry;

class RegistryTest extends \lithium\test\Unit {

	public function testSingleKey(){
		Registry::delete();

		$this->assertNull(Registry::get('anything'));

		$expected = 'moo';
		Registry::set('Cow', $expected);
		$result = Registry::get('Cow');
		$this->assertIdentical($expected, $result);

		$this->assertNull(Registry::get('Cow.noise'));

		$expected = 'baa';
		Registry::set('Sheep', $expected);
		$result = Registry::get('Sheep');
		$this->assertIdentical($expected, $result);

		$expected = array('Cow', 'Sheep');
		$result = Registry::keys();
		$this->assertIdentical($expected, $result);

		$expected = array();
		$result = Registry::keys('Cow');
		$this->assertIdentical($expected, $result);

		$expected = null;
		Registry::delete('Cow');
		$result = Registry::get('Cow');
		$this->assertIdentical($expected, $result);

		$expected = array('Sheep');
		$result = Registry::keys();
		$this->assertIdentical($expected, $result);

		$expected = array();
		Registry::delete();
		$result = Registry::get();
		$this->assertIdentical($expected, $result);

		$expected = array('Cow' => 'moo');
		Registry::merge($expected);
		$result = Registry::get();
		$this->assertIdentical($expected, $result);

		$merge = array('Sheep' => 'baa');
		$expected += $merge;
		Registry::merge($merge);
		$result = Registry::get();
		$this->assertIdentical($expected, $result);

	}

	public function testFlush(){
		$expected = Registry::get();
		$result = Registry::flush();
		$this->assertIdentical($expected, $result);

		$result = Registry::flush();
		$this->assertIdentical(array(), $result);

		Registry::set($expected);
	}

	public function testNestedKey(){
		$expected = array('noise' => 'moo');
		Registry::set('Cow', $expected);
		$result = Registry::get('Cow');
		$this->assertIdentical($expected, $result);

		$expected = 'moo';
		$result = Registry::get('Cow.noise');
		$this->assertIdentical($expected, $result);

		$expected = 'MOOO!!';
		Registry::set('Cow.noise', $expected);
		$result = Registry::get('Cow.noise');
		$this->assertIdentical($expected, $result);

		$expected = array();
		Registry::delete('Cow.noise');
		$result = Registry::get('Cow');
		$this->assertIdentical($expected, $result);

		Registry::delete();
	}

	public function testNumericKeys(){
		$expected = array('Cow', '1' => 'Sheep');
		Registry::set(0, $expected);
		$result = Registry::get(0);
		$this->assertIdentical($expected, $result);

		$result = Registry::get('0.0');
		$this->assertIdentical($expected[0], $result);

		$result = Registry::get('0.1');
		$this->assertIdentical($expected[1], $result);

		$expected = array('name' => 'Chicken','noises' => array('cluck', '1' => 'boo!'));
		Registry::set('0.2', $expected);
		$result = Registry::get('0.2');
		$this->assertIdentical($expected, $result);

		$result = Registry::get('0.2.name');
		$this->assertIdentical($expected['name'], $result);

		$result = Registry::get('0.2.noises');
		$this->assertIdentical($expected['noises'], $result);

		$result = Registry::get('0.2.noises.0');
		$this->assertIdentical($expected['noises'][0], $result);

		$result = Registry::get('0.2.noises.1');
		$this->assertIdentical($expected['noises'][1], $result);

		$expected = array(0);
		$result = Registry::keys();
		$this->assertIdentical($expected, $result);

		$expected = array(0, 1, 2);
		$result = Registry::keys(0);
		$this->assertIdentical($expected, $result);

		$expected = array(0, 1);
		$result = Registry::keys('0.2.noises');
		$this->assertIdentical($expected, $result);

		Registry::delete();
	}

	public function testSaved(){

		$file = LITHIUM_APP_PATH . '/resources/registry.test.php';
		$this->skipIf(!is_writable(dirname($file)));

		$values  = array(
			'Cow' => array('noises' => array('moo')),
			'Sheep' => array('noises' => array('baa', 'other' => 'bleet')),
			'Chicken' => array('cluck')
		);
		Registry::set($values);

		$this->assertTrue(Registry::save($file));
		Registry::delete();
		$this->assertNull(Registry::get('Cow'));
		Registry::load($file);

		$expected = $values['Cow'];
		$result = Registry::get('Cow');
		$this->assertIdentical($expected, $result);

		$this->assertTrue(Registry::save($file, array(), array('path' => 'Sheep')));

		$another = 'BAAA!!';
		Registry::set('Sheep.noises.other', $another);
		Registry::set('Sheep.noises.another', $values['Sheep']['noises']['other']);
		Registry::load($file, array(), array('path' => 'Sheep', 'merge' => true));
		$expected = $values['Sheep']['noises']['other'];
		$result = Registry::get('Sheep.noises.other');
		$this->assertIdentical($expected, $result);
		$result = Registry::get('Sheep.noises.another');
		$this->assertIdentical($expected, $result);
		@unlink($file);
	}

}