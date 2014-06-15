<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_assetic\extensions\helper;

class Assetic extends \lithium\template\Helper {

	protected $_packages = array();

	protected $_classes = array(
		'package' => 'sli_assetic\extensions\Assetic\Package'
	);

	protected function _init() {
		parent::_init();
	}

	public function info() {}

	public function styles($package = 'app', $options = array()) {
		return $this->_package($package, array('type' => 'css') + $options);
	}

	public function scripts($package = 'app', $options = array()) {
		return $this->_package($package, array('type' => 'js') + $options);
	}

	protected function _package($package, $options = array()) {
		if (!isset($this->_packages[$package])) {
			$this->_packages[$package] = $this->_instance('package', $options);
		}
		return $this->_packages[$package];
	}


	public function style($path, $options = array()) {}

	public function script($path, $options = array()) {}

	public function image($path, $options = array()) {}
}
?>