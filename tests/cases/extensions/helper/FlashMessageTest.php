<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\tests\cases\extensions\helper;

use lithium\test;

use lithium\storage\Session;
use lithium\template\View;
use lithium\template\view\adapter\File;
use lithium\tests\mocks\template\MockRenderer;
use sli_util\action\FlashMessage as Storage;
use sli_util\extensions\helper\FlashMessage;

class FlashMessageTest extends \lithium\test\Unit {

	public $message = '';

	public function _init() {
		Session::config(array(
			'default' => array(
				'adapter' => 'Php'
			)
		));
	}

	public function setUp() {
		$message = 'this is a message';
		$this->message = $message;
		Storage::write(compact('message'));
		Storage::error(compact('message'));
		Storage::success(compact('message') + array('class' => 'complete'));
		$params = array(
			'context' => new MockRenderer(array(
				'view' => new View(array(
					'loader' => new File(array(
						'paths' => array(
							'element' => '{:library}/views/elements/{:template}.{:type}.php'
						)
					))
				))
			)),
		);
		$this->helper = new FlashMessage($params);
	}

	public function tearDown() {
		Storage::clear('default');
		Storage::clear('error');
		Storage::clear('success');
	}

	public function testClears() {
		$result = Storage::read();
		$this->assertTrue(isset($result['default']));
		$this->assertTrue(isset($result['error']));
		$this->assertTrue(isset($result['success']));
		$this->helper->output('default');
		$result = Storage::read();
		$this->assertFalse(isset($result['default']));
		$this->helper->output();
		$result = Storage::read();
		$this->assertFalse(isset($result['error']));
		$this->assertFalse(isset($result['success']));
	}

	public function testOutputSingle() {
		$message = $this->message;
		$pattern = '/\s+' . preg_quote($message).'/';
		$result = $this->helper->output('default');
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-default'),
			'regex:' . $pattern,
			'/div'
		));
		$result = $this->helper->output('error');
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-error'),
			'regex:' . $pattern,
			'/div'
		));
		$result = $this->helper->output('success');
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-success complete'),
			'regex:' . $pattern,
			'/div'
		));
	}

	public function testOutputOverload() {
		$message = $this->message;
		$pattern = '/\s+' . preg_quote($message).'/';
		$result = $this->helper->error();
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-error'),
			'regex:' . $pattern,
			'/div'
		));
		$result = $this->helper->success();
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-success complete'),
			'regex:' . $pattern,
			'/div'
		));
	}

	public function testOutputMultiple() {
		$message = $this->message;
		$pattern = '/\s+' . preg_quote($message).'/';
		$result = $this->helper->output(array('default', 'error'));
		$this->assertTags($result, array(
			array('div' => array('class' => 'flash-message flash-message-default')),
			'regex:' . $pattern,
			'/div',
			array('div' => array('class' => 'flash-message flash-message-error')),
			'regex:' . $pattern,
			'/div'
		));
	}

	public function testOutputAll() {
		$message = $this->message;
		$pattern = '/\s+' . preg_quote($message).'/';
		$result = $this->helper->output();
		$this->assertTags($result, array(
			array('div' => array('class' => 'flash-message flash-message-default')),
			'regex:' . $pattern,
			'/div',
			array('div' => array('class' => 'flash-message flash-message-error')),
			'regex:' . $pattern,
			'/div',
			array('div' => array('class' => 'flash-message flash-message-success complete')),
			'regex:' . $pattern,
			'/div'
		));
	}

	public function testPassOverides() {
		$message = $this->message;
		$pattern = '/\s+' . preg_quote($message).'/';
		$result = $this->helper->output('default', array('data' => array(
			'class' => 'custom'
		)));
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-default custom'),
			'regex:' . $pattern,
			'/div'
		));
		$message = 'This is an error message';
		$result = $this->helper->output('error', array('data' => array(
			'message' => $message
		)));
		$pattern = '/\s+' . preg_quote($message).'/';
		$this->assertTags($result, array(
			'div' => array('class' => 'flash-message flash-message-error'),
			'regex:' . $pattern,
			'/div'
		));
	}
}

?>