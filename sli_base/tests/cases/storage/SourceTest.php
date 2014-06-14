<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\storage;

use sli_base\storage\Source;

class SourceTest extends \lithium\test\Unit {

	public function testDefault(){
		$file = LITHIUM_APP_PATH . '/resources/tmp/tests/configuration.test.php';
		$this->skipIf(!is_writable(dirname($file)));

		Source::write($file, $_SERVER);
		$this->assertTrue(file_exists($file));

		$data = Source::read($file);
		$this->assertIdentical($data, $_SERVER);

		Source::delete($file);
		$this->assertFalse(file_exists($file));
	}
}

?>