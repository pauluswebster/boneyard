<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_dom\tests\cases\template;

use lithium\test;

use lithium\action\Request;
use lithium\net\http\Router;
use lithium\data\entity\Record;
use lithium\tests\mocks\template\helper\MockFormRenderer;
use sli_dom\template\Element;
use sli_dom\template\element\Literal;
use sli_dom\template\element\Html;
use sli_dom\template\element\html\Image;

class ElementTest extends \lithium\test\Unit {

	public function setUp() {
		$this->_routes = Router::get();
		Router::reset();
		Router::connect('/{:controller}/{:action}/{:id}.{:type}', array('id' => null));
		Router::connect('/{:controller}/{:action}/{:args}');

		$request = new Request();
		$request->params = array('controller' => 'posts', 'action' => 'index');
		$request->persist = array('controller');

		$this->context = new MockFormRenderer(compact('request'));
	}

	public function tearDown() {
		Router::reset();

		foreach ($this->_routes as $route) {
			Router::connect($route);
		}
		unset($this->_routes, $this->context);
	}

	public function testCreate() {
		$element1 = new Element();
		$element2 = Element::create();
		$this->assertEqual($element1, $element2);
		$element1 = new Literal();
		$element2 = Element::create('Literal');
		$this->assertEqual($element1, $element2);
		$element1 = new Html();
		$element2 = Element::create('Html');
		$this->assertEqual($element1, $element2);
		$element1 = new Image();
		$element2 = Element::create('html\Image');
		$this->assertEqual($element1, $element2);
	}

	public function testAttributes() {
		$attr = array('class' => 'test', 'id' => 'Success');
		$element1 = new Element();
		$element1->attributes($attr);
		$this->assertIdentical($attr, $element1->attributes());

		$attr = array('class' => 'test', 'id' => 'Success');
		$element2 = new Element();
		$element2->attr($attr);
		$this->assertIdentical($attr, $element2->attr());
		$this->assertIdentical($element1->attributes(), $element2->attr());
	}

	public function testParams() {
		$attr = array('path' => 'some value', 'encoding' => 'utf');
		$element1 = new Element();
		$element1->params($attr);
		$this->assertIdentical($attr, $element1->params());
	}

	public function testContext() {
		$element = new Element();
		$expected = null;
		$result = $element->context();
		$this->assertIdentical($expected, $result);

		$expected = null;
		try {
			$result = $element->context(true);
		} catch(\RuntimeException $e) {
			$result = null;
		}
		$this->assertTrue($e);
		$this->assertIdentical($expected, $result);

		$expected = $this->context;
		$element->context($this->context);
		$result = $element->context();
		$this->assertIdentical($expected, $result);

		$element = new Element(array(
			'context' => $this->context
		));
		$expected = $this->context;
		$result = $element->context();
		$this->assertIdentical($expected, $result);
	}

	public function testRenderSimple() {
		$element = new Element(array(
			'context' => $this->context,
			'params' => array(
				'content' => 'This is the content'
			)
		));
		$result = $element->render();
		$expected = 'This is the content';
		$this->assertEqual($expected, $result);

		$element = new Element(array(
			'context' => $this->context,
			'params' => array(
				'before' => 'Before,',
				'content' => ' content,',
				'after' => 'after...'
			),
			'template' => '{:before}{:content} {:after}'
		));

		$result = $element->render();
		$expected = 'Before, content, after...';
		$this->assertEqual($expected, $result);

		$element = new Element(array(
			'context' => $this->context,
			'params' => array(
				'title' => 'Hello',
				'content' => 'Hello world.'
			),
			'attributes' => array('class' => 'hello-world'),
			'template' => '<div{:options}><h1>{:title}</h1><p>{:content}</p></div>'
		));
		$result = $element->render();
		$expected = '<div class="hello-world"><h1>Hello</h1><p>Hello world.</p></div>';
		$this->assertEqual($expected, $result);
		$result = "$element";
		$this->assertEqual($expected, $result);
	}

	public function testRenderContent() {
		$element = new Element(array(
			'context' => $this->context,
			'template' => '<div{:options}>{:content}</div>',
			'attributes' => array('id' => 'hello-user')
		));
		$result = $element->render();
		$expected = '<div id="hello-user"></div>';
		$this->assertEqual($expected, $result);

		$element2 = new Element(array(
			'parent' => $element,
			'params' => array(
				'name' => 'Tester',
			),
			'template' => '<p>Hello {:name},<br />{:content}</p>'
		));

		$result = $element->render();
		$expected = '<div id="hello-user"><p>Hello Tester,<br /></p></div>';
		$this->assertEqual($expected, $result);

		$element3 = new Element(array(
			'parent' => $element2,
			'params' => array(
				'content' => 'This is the message.',
			),
		));
		$result = $element->render();
		$expected = '<div id="hello-user"><p>Hello Tester,<br />This is the message.</p></div>';
		$this->assertEqual($expected, $result);


		$element4 = new Element(array(
			'parent' => $element,
			'params' => array(
				'content' => '<p>Thanks.</p>',
			)
		));
		$result = $element->render();
		$expected = '<div id="hello-user"><p>Hello Tester,<br />This is the message.</p><p>Thanks.</p></div>';
		$this->assertEqual($expected, $result);
		$result = "$element";
		$this->assertEqual($expected, $result);
	}

	public function testArray() {
		$e = new Element(array(
			'params' => array('title' => 'Users'),
			'attributes' => array('page' => 1),
			'children' => array(
				new Element(array(
					'params' => array('name' =>'First User')
				)),
				new Element(array(
					'params' => array('name' =>'Second User')
				))
			)
		));
		$expected = array (
			'title' => 'Users',
			'options' =>	array ('page' => 1),
			'content' => array (
				array (
					'name' => 'First User',
					'options' => array (),
					'content' => array (),
				),
				array (
					'name' => 'Second User',
					'options' => array (),
					'content' => array (),
				)
			)
		);
		$result = $e->to('array');
		$this->assertIdentical($expected, $result);
	}

	public function testJson() {
		$e = new Element(array(
			'params' => array('title' => 'Users'),
			'attributes' => array('page' => 1),
			'children' => array(
				new Element(array(
					'params' => array('name' =>'First User')
				)),
				new Element(array(
					'params' => array('name' =>'Second User')
				))
			)
		));
		$expected = '{"title":"Users","options":{"page":1},"content":[{"name"';
		$expected.= ':"First User","options":[],"content":[]},{"name":"Second';
		$expected.= ' User","options":[],"content":[]}]}';
		$result = $e->to('json');
		$this->assertIdentical($expected, $result);
	}

	public function testExport() {}
}
?>