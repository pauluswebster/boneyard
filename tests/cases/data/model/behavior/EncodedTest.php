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
use sli_base\data\model\behavior\Encoded;

class EncodedTest extends \lithium\test\Unit {

	protected static $model = 'sli_base\tests\mocks\models\MockPost';

	public function _init() {
		Connections::add('mock-source', array('type' => '\sli_base\tests\mocks\data\MockSource'));
	}

	public function setUp() {
		$model = static::$model;
		$model::resetFilters();
		Connections::get('mock-source')->flush();
	}

	public function testBase64Encdoe(){
		$model = static::$model;
		Encoded::apply($model, array(
			'base64' => 'body'
		));
		$body = 'This is some text';

		$post = $model::create(compact('body'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);

		$post->save();
		$expected = base64_encode($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$result = $post->data('body');
		$this->assertEqual($body, $result);
	}

	public function testUrlEncode() {
		$model = static::$model;
		Encoded::apply($model, array(
			'url' => 'body'
		));
		$body = 'This is some text';

		$post = $model::create(compact('body'));
		$result = $post->data('body');
		$this->assertEqual($body, $result);

		$post->save();
		$expected = urlencode($body);
		$result = $post->data('body');
		$this->assertEqual($expected, $result);

		$post = $model::first();
		$result = $post->data('body');
		$this->assertEqual($body, $result);
	}
}

?>