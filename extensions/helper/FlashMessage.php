<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\extensions\helper;


class FlashMessage extends \lithium\template\Helper {

	/**
	 * Class dependencies
	 *
	 * @var $_classes array
	 */
	protected $_classes = array(
		'storage' => 'sli_util\action\FlashMessage'
	);

	/**
	 * Default output config
	 *
	 * @var $_config array
	 */
	protected $_config = array(
		'type' => 'element',
		'template' => 'flash_message',
		'data' => array(),
		'options' => array(
			'library' => 'slicedup_core'
		)
	);

	public function __construct(array $config = array()) {
		parent::__construct($config + $this->_config);
	}

	/**
	 * Overload caller to enable direct output of flash message by key.
	 * For example the following are equivelant:
	 *
	 * `$this->flashMessage->output('error');`
	 * `$this->flashMessage->error();`
	 *
	 * @param string $method
	 * @param array $params
	 * @return string
	 */
	public function __call($method, $params = array()) {
		$key = $method;
		$options = isset($params[0]) ? $params[0] : array();
		return $this->output($key, $options);
	}

	/**
	 * Output flash message(s)
	 *
	 * For Example:
	 *
	 * Output error message
	 * `$this->flashMessage->output();`
	 *
	 * Output error message
	 * `$this->flashMessage->output('error');`
	 *
	 * Output error & auth messages
	 * `$this->flashMessage->output(array('error', 'auth'));`
	 *
	 * @param mixed $key
	 * @param array $options
	 * @return string
	 */
	public function output($key = null, array $options = array()) {
		$options += $this->config();
		$storage = $this->storage();
		$config = $storage::config();
		$view = $this->_context->view();
		$type = array($options['type'] => $options['template']);

		if ($key && is_string($key)) {
			$flash = array($key => $storage::read($key, $config));
		} else {
			$flash = $storage::read(null, $config);
			if ($key) {
				$flash = array_intersect_key($flash, array_fill_keys($key, null));
			}
		}

		$output = '';
		if ($flash = array_filter((array) $flash)) {
			foreach ($flash as $key => $message) {
				$storage::clear($key, $config);
				$data = $options['data'] + $message + array('class' => '');
				$class = $key . ($data['class'] ? " {$data['class']}": '');
				$data['class'] = $class;
				$output.= $view->render($type, $data, $options['options']);
			}
		}
		return $output;
	}

	/**
	 * Config setter/getter
	 *
	 * @param array $config
	 * @return array
	 */
	public function config(array $config = array()) {
		if ($config) {
			$this->_config = $config + $this->_config;
		}
		return $this->_config;
	}

	/**
	 * Set storage class config / get storage class name
	 *
	 * @param array $config
	 * @return string storage class
	 */
	public function storage(array $config = array()) {
		$storage = $this->_classes['storage'];
		if ($config) {
			$storage::config($config);
		}
		return $storage;
	}
}

?>