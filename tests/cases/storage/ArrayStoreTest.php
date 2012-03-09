<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\storage;

use sli_base\storage\ArrayStore;

class ArrayStoreTest extends \lithium\test\Unit {

	public function testAccess(){
		$data = array(
			'Farm',
			'Foo' => 'Bar',
			'Cows' => 'Moo',
			'Pig' => 'Oink',
			7 => 'Seven'
		);
		$store = new ArrayStore(compact('data'));

		$expected = $data;
		$result = $store->get();
		$this->assertEqual($expected, $result);
		$result = $store->data();
		$this->assertEqual($expected, $result);

		$expected = $data[0];
		$result = $store->get(0);
		$this->assertEqual($expected, $result);
		$expected = $data[0];
		$result = $store->get('0');
		$this->assertEqual($expected, $result);

		$more = array(
			'exotic' => array(
				'cats' => array(
					'tigers',
					'lions'
				)
			)
		);

		$store2 = clone $store;
		$store->set(null, $more, true);
		$store2->merge(null, $more);

		$expected = $data + $more;
		$result = $store->get();
		$this->assertEqual($expected, $result);

		$expected = $store->get();
		$result = $store2->get();
		$this->assertEqual($expected, $result);

		$store->delete('exotic.cats.0');
		$expected = array(1 => 'lions');
		$result = $store->get('exotic.cats');
		$this->assertEqual($expected, $result);

		$expected = array('exotic' => array('cats' => array(1 => 'lions')));
		$result = $store->extract('exotic.cats');
		$this->assertEqual($expected, $result);
	}

	public function testArrayAccess() {
		$data = array(
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
				)
			)
		);
		$store = new ArrayStore(compact('data'));

		$expected = $data['app'];
		$result = $store['app'];
		$this->assertEqual($expected, $result);

		$expected = $data['app']['name'];
		$result = $store['app.name'];
		$this->assertEqual($expected, $result);

		$expected = $data['app']['config']['x'][1];
		$result = $store['app.config.x.1'];
		$this->assertEqual($expected, $result);

		$store['app.version'] = 2;
		$this->assertTrue(isset($store['app.version']));
		$expected = $data['app'] + array('version' => 2);
		$result = $store['app'];
		$this->assertEqual($expected, $result);

		unset($store['app.version']);
		$this->assertFalse(isset($store['app.version']));
		$this->assertEqual($expected, $result);
	}
}

?>