<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\util;

class Text extends \lithium\core\StaticObject {

	public static function truncate($text, $length, $options = array()) {
		if (is_string($options)) {
			$options = array('ending' => $options);
		} else {
			$options = (array) $options;
		}
		$options += array('html' => false, 'ending' => '...', 'strict' => false);
		if ($options['html']) {
			return Html::truncate($text, $length, $options);
		}
		extract($options);
		if (mb_strlen($text) <= $length) {
			return $text;
		} else {
			$truncated = mb_substr($text, 0, $length - mb_strlen($ending));
		}
		if (!$strict) {
			$spacepos = mb_strrpos($truncated, ' ');
			if (isset($spacepos)) {
				$truncated = mb_substr($truncated, 0, $spacepos);
			}
		}
		return $truncated . $ending;
	}
}

?>