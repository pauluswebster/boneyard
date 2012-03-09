<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\data\model\behavior;

use lithium\data\Connections;
use sli_base\core\Behaviors;
use sli_base\data\model\behavior\Modified;

class ModifiedTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPost';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
		Connections::get('mock-source')->flush();
	}

	public function testBasicFilters(){
		$model = static::$model;
		$applied = Modified::apply($model, array(
			'fields' => array(
				'body' => array(
					'create' => 'strtoupper',
					'validates' => function($body) {
						return ucwords(strtolower($body));
					},
					'save' => array(array($this, 'objMethod')),
					'find' => get_class($this) . '::staticMethod'
				)
			)
		));

		$body = 'apples bananas oranges';

		$post = $model::create(compact('body'));
		$expected = strtoupper($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post->validates();
		$expected = ucwords(strtolower($expected));
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post->save();
		$expected = $this->objMethod($expected);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$expected = self::staticMethod($expected);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);
	}

	public function testFilterArrayArgs() {
		$model = static::$model;

		$filter1 = function($value, $arg1 = null, $arg2 = null){
			return join('|', func_get_args());
		};

		$filter2 = function($arg1, $value, $arg2 = null){
			return join('|', func_get_args());
		};

		$applied = Modified::apply($model, array(
			'fields' => array(
				'title' => array(
					'create' => array($filter1, 'arg1', 'arg2'),
					'save' => array($filter1, 'arg1', '{:field}', 'arg2'),
				)
			)
		));

		$title = 'This is the title.';

		$post = $model::create(compact('title'));
		$expected = $filter1($title, 'arg1', 'arg2');
		$result = $post->data('title');
		$this->assertEqual($expected, $result);

		$post->save();
		$expected = $filter2('arg1', $expected, 'arg2');
		$result = $post->data('title');
		$this->assertEqual($expected, $result);
	}

	public function testFullFilterArray() {
		$model = static::$model;

		$applied = Modified::apply($model, array(
			'fields' => array(
				'body' => array(
					'create' => array(
						'call' => 'strtoupper',
						'_map' => '_body'
					),
					'validates' => array(
						'call' => function($body, $end = '') {
							return ucwords(strtolower($body)) . $end;
						},
						'args' => array(' end.')
					),
					'save' => array(
						'call' => array($this, 'objMethod')
					),
					'find' => array(
						'call' => get_class($this) . '::staticMethod',
						'map' => 'body_'
					)
				)
			)
		));

		$body = 'apples bananas oranges';

		$post = $model::create(compact('body'));
		$expected = $body;
		$result = $post->data('_body');
		$this->assertEqual($expected, $result);
		$expected = strtoupper($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post->validates();
		$expected = $body;
		$result = $post->data('_body');
		$this->assertEqual($expected, $result);
		$expected = ucwords(strtolower($expected)) . ' end.';
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post->save(null, array('validate' => false));
		$expected = $this->objMethod($expected);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$expected = self::staticMethod($expected);
		$result = $post->data('body_');
		$this->assertEqual($expected, $result);
	}

	public function testMultiApplied() {
		$model = static::$model;
		Modified::apply($model, array(
			'fields' => array(
				'body' => array(
					'save' => array($this, 'objMethod'),
					'find' => get_class($this) . '::staticMethod'
				)
			)
		));
		Modified::apply($model, array(
			'fields' => array(
				'body' => array(
					'save' => get_class($this) . '::staticMethod',
					'find' => array($this, 'objMethod')
				)
			)
		));

		$body = 'apples bananas oranges';

		$post = $model::create(compact('body'));
		$expected = $body;
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post->save();
		$expected = $body;
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$expected = $body;
		$result = $post->data('body');
		$this->assertEqual($expected, $result);
	}

	public function testModifiers() {
		$model = static::$model;
		Modified::modifiers(array('testMod' => array(
			'save' => array($this, 'objMethod'),
			'find' => get_class($this) . '::staticMethod'
		)));
		Modified::apply($model, array(
			'fields' => array(
				'body' => 'testMod'
			),
			'testMod' => array(
				'title'
			)
		));

		$body = 'apples bananas oranges';
		$title = 'I only like apples.';

		$post = $model::create(compact('body', 'title'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);
		$result = $post->data('title');
		$this->assertEqual($title, $result);

		$post->save();
		$body = $this->objMethod($body);
		$result = $post->data('body');
		$this->assertEqual($body, $result);
		$title = $this->objMethod($title);
		$result = $post->data('title');
		$this->assertEqual($title, $result);

		$post = $model::first();
		$body = self::staticMethod($body);
		$result = $post->data('body');
		$this->assertEqual($body, $result);
		$title = self::staticMethod($title);
		$result = $post->data('title');
		$this->assertEqual($title, $result);

		$posts = $model::all();
		$body = self::staticMethod($body);
		$result = $posts[1]->data('body');
		$this->assertEqual($body, $result);
		$title = self::staticMethod($title);
		$result = $posts[1]->data('title');
		$this->assertEqual($title, $result);
	}

	public function objMethod($value) {
		return str_ireplace(array('a', 'e'), array('4', '3'), $value);
	}

	public static function staticMethod($value) {
		return str_replace(array('4', '3'), array('a', 'e'), $value);
	}
}

?>