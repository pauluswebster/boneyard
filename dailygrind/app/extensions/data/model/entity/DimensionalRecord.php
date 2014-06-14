<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\extensions\data\model\entity;

class DimensionalRecord extends \lithium\data\entity\Record {

	/**
	 * (non-PHPdoc)
	 * @see lithium\data.Entity#__get()
	 */
	public function &__get($name) {
		if (strpos($name, '.')) {
			return $this->_getNested($name);
		}
		return parent::__get($name);
	}

	/**
	 *
	 * @param unknown_type $name
	 */
	protected function &_getNested($name) {
		$current = $this;
		$null = null;
		$path = explode('.', $name);
		$current = $this->__get($path[0]);
		if (!is_array($current) && !$current instanceOf \ArrayAccess) {
			return $current;
		}
		array_shift($path);
		$length = count($path) - 1;
		foreach ($path as $i => $key) {
			if (!isset($current[$key])) {
				return $null;
			}
			$current = $current[$key];
			if (is_scalar($current) && $i < $length) {
				return $null;
			}
		}
		return $current;
	}

}

?>