<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\cases\data\model\behavior;

use lithium\data\Connections;
use sli_base\data\model\behavior\Serialized;

class SerializedTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPost';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
		Connections::get('mock-source')->flush();
	}

	public function testSerialize(){
		$model = static::$model;
		Serialized::apply($model, array(
			'serialize' => 'body'
		));
		$body = array(
			'h1' => 'This is the title',
			'This is some text'
		);

		$post = $model::create(compact('body'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);

		$post->save();
		$expected = serialize($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$result = $post->data('body');
		$this->assertEqual($body, $result);
	}

	public function testJson() {
		$model = static::$model;
		Serialized::apply($model, array(
			'json' => 'body'
		));
		$body = array(
			'h1' => 'This is the title',
			'This is some text'
		);

		$post = $model::create(compact('body'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);

		$post->save();
		$expected = json_encode($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$result = $post->data('body');
		$this->assertEqual($body, $result);
	}

	public function testJsonObj() {
		$model = static::$model;
		Serialized::apply($model, array(
			'jsonObj' => 'body'
		));
		$body = array(
			'h1' => 'This is the title',
			'body' => 'This is some text'
		);

		$post = $model::create(compact('body'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);

		$post->save();
		$expected = json_encode($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$expected = (object) $body;
		$result = $post->data('body');
		$this->assertEqual($expected, $result);
	}
}

?>