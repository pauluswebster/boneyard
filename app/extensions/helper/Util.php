<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\extensions\helper;

use lithium\core\Libraries;

class Util extends \lithium\template\Helper {

	protected $_utils = array();

	public function __get($param) {}

	public function __call($method, $params = array()) {}

	protected function _locate($class) {}
}
?>