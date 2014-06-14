<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\extensions\strategy\storage\source;

use sli_base\storage\Source;

class IniTest extends \lithium\test\Unit {

	function testRead(){

		$file = LITHIUM_APP_PATH . '/resources/storage.test.ini';

		$this->skipIf(!is_writable(dirname($file)));
		$ini = "value=this\narray[] = yes\n[section]\nvalue = one";
		file_put_contents($file, $ini);

		Source::config(Source::config() + array(
			'ini' => array(
				'adapter' => 'File',
				'strategies' => array(
					'Ini'
				)
			)
		));

		$data = Source::read($file, array('name' => 'ini', 'sections' => true));
		$this->assertTrue(!empty($data));

		$this->assertTrue(isset($data['value']));
		$this->assertEqual($data['value'], 'this');

		$this->assertTrue(is_array($data['section']));
		$this->assertTrue(isset($data['section']['value']));
		$this->assertEqual($data['section']['value'], 'one');

		$this->assertTrue(is_array($data['array']));
		$this->assertTrue(isset($data['array'][0]));
		$this->assertEqual($data['array'][0], 1);
		$this->assertEqual($data['array'][0], true);
		$this->assertIdentical($data['array'][0], '1');

		@unlink($file);
	}
}

?>