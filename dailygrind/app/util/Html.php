<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\util;

class Html extends \lithium\core\StaticObject {

	public static function truncate($text, $length, array $options = array()) {
		$options += array('html' => true, 'ending' => '', 'strict' => false);
		if (!$options['html']) {
			return Text::truncate($text, $length, $options);
		}
		return $text;
	}
}

?>