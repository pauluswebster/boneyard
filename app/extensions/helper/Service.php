<?php
namespace app\extensions\helper;

use sli_util\storage\Registry;

class Service extends \lithium\template\Helper {
	
	protected $_serviceUrl;
	
	protected function _init() {
		parent::_init();
		$serviceHost = Registry::get('env.hosts.service');
		$this->_serviceUrl = "{$serviceHost['scheme']}://{$serviceHost['host']}";
	}

	/**
	 * Get a link to the service app
	 */
	public function url($segment = '') {
		return "{$this->_serviceUrl}/$segment";
	}
	
	/**
	 * Get a redirect from the service app for a given url
	 */
	public function redirect($url = '') {}
}

?>