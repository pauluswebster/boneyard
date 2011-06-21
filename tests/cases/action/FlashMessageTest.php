<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\tests\cases\action;

use lithium\storage\Session;
use sli_util\action\FlashMessage;

class FlashMessageTest extends \lithium\test\Unit {

	public function _init() {
		Session::config(array(
			'default' => array(
				'adapter' => 'Php'
			)
		));
	}

	public function testStorage() {
		$message = 'this is a message';

		$result = FlashMessage::write($message);
		$this->assertTrue($result);

		$expected = Session::read('FlashMessages.default');
		$this->assertTrue(!empty($expected));
		$result = FlashMessage::read('default');
		$this->assertEqual($expected, $result);

		FlashMessage::clear('default');
		$expected = Session::read('FlashMessages.default');
		$this->assertTrue(empty($expected));
		$result = FlashMessage::read('default');
		$this->assertEqual($expected, $result);

		$message = compact('message');

		$result = FlashMessage::write($message, 'error');
		$this->assertTrue($result);

		$expected = Session::read('FlashMessages.error');
		$this->assertTrue(!empty($expected));
		$result = FlashMessage::read('error');
		$this->assertEqual($expected, $result);
	}

	public function testOverloaded() {
		$message = 'this is a message';

		$result = FlashMessage::error($message);
		$this->assertTrue($result);

		$expected = Session::read('FlashMessages.error');
		$this->assertTrue(!empty($expected));
		$result = FlashMessage::error();
		$this->assertEqual($expected, $result);
	}
}

?>