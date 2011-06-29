<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_dom\template\element;

class Literal extends \sli_dom\template\Element {

	protected $_params = array(
		'content' => ''
	);

	protected function _init() {
		parent::_init();
		if (isset($this->_config['content'])) {
			$this->_params['content'] = $this->_config['content'];
		}
	}

	public function content($content = null) {
		if (isset($content)) {
			$this->_params['content'] = (string) $content;
		}
		return $this->_params['content'];
	}

	protected function _render() {
		return $this->content();
	}
}

?>