<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app;

use slicedup_core\configuration\Source;

class App extends \lithium\core\StaticObject {
	
	public static function announce($message = null) {
		$path = LITHIUM_APP_PATH . '/resources/announce.php';
		static $announce;
		if (!isset($announce)) {
			$config = Source::read($path);
			$announce = $config ?: array('message' => '');
		}
		if (isset($message)) {
			$announce = compact('message') + $announce;
			return Source::write($path, $announce);
		}
		return $announce['message'];
	}

}

?>