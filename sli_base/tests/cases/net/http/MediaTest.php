<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\net\http;

use sli_base\net\http\Media;

class MediaTest extends \lithium\test\Unit {

	public function setUp() {
		Media::type('testMedia', 'test/media', array(
			'paths' => array(
				'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
				'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
				'element'  => '{:library}/views/elements/{:template}.{:type}.php'
			)
		));
	}

	public function testAdd(){
		$base = Media::type('testMedia');
		$reset = function(){
			Media::setPaths('testMedia', array(
				'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
				'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
				'element'  => '{:library}/views/elements/{:template}.{:type}.php'
			));
		};
		//invalid media type
		$result = Media::addPaths('invalidType', array(
			'template' => '{:library}/views/testone/{:template}.{:type}.php'
		));
		$this->assertFalse($result);

		//add single, string target
		$add = Media::addPaths('testMedia', array(
			'template' => '{:library}/views/testone/{:template}.{:type}.php'
		));
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $add);
		$expected = array(
			'{:library}/views/testone/{:template}.{:type}.php',
			'{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$result = $config['options']['paths']['template'];
		$this->assertEqual($expected, $result);

		//append
		$reset();
		$add = Media::addPaths('testMedia', array(
			'template' => '{:library}/views/testone/{:template}.{:type}.php'
		), false);
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $add);
		$expected = array(
			'{:library}/views/{:controller}/{:template}.{:type}.php',
			'{:library}/views/testone/{:template}.{:type}.php'
		);
		$result = $config['options']['paths']['template'];
		$this->assertEqual($expected, $result);

		//duplicate template
		$add = Media::addPaths('testMedia', array(
			'template' => '{:library}/views/testone/{:template}.{:type}.php'
		));
		$config = Media::type('testMedia');
		$result = $config['options']['paths']['template'];
		$this->assertEqual($expected, $result);

		$reset();
		//add single, array target
		$add = Media::addPaths('testMedia', array(
			'template' => '{:library}/views/testtwo/{:template}.{:type}.php'
		));
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $add);
		$expected = array(
			'{:library}/views/testtwo/{:template}.{:type}.php',
			'{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$result = $config['options']['paths']['template'];
		$this->assertEqual($expected, $result);

		$reset();
		//add multiple mixed targets
		$add = Media::addPaths('testMedia', array(
			'template' => array(
				'{:library}/views/testfour/{:template}.{:type}.php',
				'{:library}/views/testthree/{:template}.{:type}.php',
				'{:library}/views/testthree/{:template}.{:type}.php'
			),
			'layout' => array(
				'{:library}/views/layouts/testtwo/{:layout}.{:type}.php',
				'{:library}/views/layouts/testone/{:layout}.{:type}.php'
			)
		));
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $add);
		$expected = array(
			'{:library}/views/testfour/{:template}.{:type}.php',
			'{:library}/views/testthree/{:template}.{:type}.php',
			'{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$result = $config['options']['paths']['template'];
		$this->assertEqual($expected, $result);
		$expected = array(
			'{:library}/views/layouts/testtwo/{:layout}.{:type}.php',
			'{:library}/views/layouts/testone/{:layout}.{:type}.php',
			'{:library}/views/layouts/{:layout}.{:type}.php'
		);
		$result = $config['options']['paths']['layout'];
		$this->assertEqual($expected, $result);

		//add new config
		$add = Media::addPaths('testMedia', array(
			'widget' => array(
				'{:library}/views/widgets/{:template}.{:type}.php'
			)
		));
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $add);
		$this->assertTrue(isset($config['options']['paths']['widget']));
		$expected = array(
			'{:library}/views/widgets/{:template}.{:type}.php'
		);
		$result = $config['options']['paths']['widget'];
		$this->assertEqual($expected, $result);
	}


	public function testRemove() {
		$paths = array(
			'template' => array(
				'{:library}/views/testfour/{:template}.{:type}.php',
				'{:library}/views/testthree/{:template}.{:type}.php',
				'{:library}/views/testtwo/{:template}.{:type}.php',
				'{:library}/views/testone/{:template}.{:type}.php',
				'{:library}/views/{:controller}/{:template}.{:type}.php'
			),
			'layout' => array(
				'{:library}/views/layouts/testthree/{:layout}.{:type}.php',
				'{:library}/views/layouts/testtwo/{:layout}.{:type}.php',
				'{:library}/views/layouts/testone/{:layout}.{:type}.php',
				'{:library}/views/layouts/{:layout}.{:type}.php'
			),
			'element' => array(
				'{:library}/views/elements/testtwo/{:template}.{:type}.php',
				'{:library}/views/elements/testone/{:template}.{:type}.php',
				'{:library}/views/elements/{:template}.{:type}.php'
			),
			'widget' => array(
				'{:library}/views/widgets/{:template}.{:type}.php'
			)
		);
		Media::addPaths('testMedia', $paths);

		//invalid media type
		$result = Media::removePaths('invalidType', array(
			'template' => '{:library}/views/testone/{:template}.{:type}.php'
		));
		$this->assertFalse($result);

		//remove string path
		$remove = Media::removePaths('testMedia', '{:library}/views/testthree/{:template}.{:type}.php');
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $remove);
		$result = $config['options']['paths']['template'];
		$expected = array(
			0 => '{:library}/views/testfour/{:template}.{:type}.php',
			2 => '{:library}/views/testtwo/{:template}.{:type}.php',
			3 => '{:library}/views/testone/{:template}.{:type}.php',
			4 => '{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$this->assertEqual($expected, $result);

		//remove array path
		$remove = Media::removePaths('testMedia', array(
			'template' => '{:library}/views/testfour/{:template}.{:type}.php',
			'layout' => array(
				'{:library}/views/layouts/testone/{:layout}.{:type}.php',
				'{:library}/views/layouts/testthree/{:layout}.{:type}.php'
			)
		));
		$config = Media::type('testMedia');
		$this->assertEqual($config['options']['paths'], $remove);
		$result = $config['options']['paths']['template'];
		$expected = array(
			2 => '{:library}/views/testtwo/{:template}.{:type}.php',
			3 => '{:library}/views/testone/{:template}.{:type}.php',
			4 => '{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$this->assertEqual($expected, $result);
		$result = $config['options']['paths']['layout'];
		$expected = array(
			1 => '{:library}/views/layouts/testtwo/{:layout}.{:type}.php',
			3 => '{:library}/views/layouts/{:layout}.{:type}.php'
		);
		$this->assertEqual($expected, $result);

		//remove with pattern, template set
		$remove = Media::removePaths('testMedia', array(
			'template' => '/testone/'
		));
		$this->assertEqual($config['options']['paths'], $remove);

		$remove = Media::removePaths('testMedia', array(
			'template' => '/testone/'
		), true);
		$this->assertEqual($config['options']['paths']['element'], $remove['element']);

		$config = Media::type('testMedia');
		$result = $config['options']['paths']['template'];
		$expected = array(
			2 => '{:library}/views/testtwo/{:template}.{:type}.php',
			4 => '{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$this->assertEqual($expected, $result);

		//remove with pattern from all
		$remove = Media::removePaths('testMedia', '/testtwo/', true);
		$config = Media::type('testMedia');
		$result = $config['options']['paths']['template'];
		$expected = array(
			4 => '{:library}/views/{:controller}/{:template}.{:type}.php'
		);
		$this->assertEqual($expected, $result);
				$result = $config['options']['paths']['layout'];
		$expected = array(
			3 => '{:library}/views/layouts/{:layout}.{:type}.php'
		);
		$this->assertEqual($expected, $result);
	}

}

?>