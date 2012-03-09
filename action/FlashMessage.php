<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\action;


class FlashMessage extends \lithium\core\StaticObject {

	/**
	 * Class dependencies.
	 *
	 * @var array
	 */
	protected static $_classes = array(
		'storage' => 'lithium\storage\Session'
	);

	/**
	 * Storage class config
	 *
	 * @var array
	 */
	protected static $_storage = array(
		'name' => 'default'
	);

	/**
	 * Overload caller to enable direct read/write of flash message by key.
	 *
	 * For example:
	 *
	 * Set error message
	 * `FlashMessage::error('There has been an error');`
	 *
	 * Get error message
	 * `$error = FlashMessage::error();`
	 *
	 * @param string $method
	 * @param array $params
	 * @return mixed result of read/write
	 */
	public static function __callStatic($method, $params) {
		$key = $method;
		$options = isset($params[1]) ? $params[1] : array();
		if (isset($params[0])) {
			return static::write($params[0], $key, $options);
		} else {
			return static::read($key, $options);
		}
	}

	/**
	 * Writes a flash message.
	 *
	 * @param string $key
	 * @param array $options
	 * @return boolean true on write, otherwise false.
	 */
	public static function write($message, $key = 'default', $options = array()) {
		$storage = static::$_classes['storage'];
		$options += static::$_storage;
		if (!is_array($message)) {
			$message = compact('message');
		}

		$params = compact('key', 'message', 'options');
		$filter = function($self, $params) use($storage) {
			extract($params);
			return $storage::write("FlashMessages.{$key}", $message, $options);
		};

		return static::_filter(__FUNCTION__, $params, $filter);
	}

	/**
	 * Reads a flash message.
	 *
	 * @param string $key
	 * @param array $options
	 * @return array flash message from storage
	 */
	public static function read($key = null, $options = array()) {
		$storage = static::$_classes['storage'];
		$options += static::$_storage;

		$params = compact('key', 'options');
		$filter = function($self, $params) use($storage) {
			extract($params);
			$storageKey = 'FlashMessages';
			if ($key) {
				$storageKey .= ".{$key}";
			}
			return $storage::read($storageKey, $options);
		};

		return static::_filter(__FUNCTION__, $params, $filter);
	}

	/**
	 * Clears one or all flash messages from the storage.
	 *
	 * @param string $key
	 * @param array $options
	 * @return null
	 */
	public static function clear($key = null, $options = array()) {
		$storage = static::$_classes['storage'];
		$options += static::$_storage;

		$params = compact('key', 'options');
		$filter = function($self, $params) use($storage) {
			extract($params);
			$storageKey = 'FlashMessages';
			if ($key) {
				$storageKey .= ".{$key}";
			}
			return $storage::delete($storageKey, $options);
		};

		return static::_filter(__FUNCTION__, $params, $filter);
	}

	/**
	 * Configure storage
	 */
	public static function config(array $config = array(), $class = null) {
		if ($config) {
			if (isset($config['class'])) {
				$class = $config['class'];
			}
			unset($config['class']);
			static::$_storage = $config + static::$_storage;
		}
		if ($class) {
			if (is_string($class)) {
				static::$_classes['storage'] = $class;
			}
			return array('class' => static::$_classes['storage']) + static::$_storage;
		}
		return static::$_storage;
	}
}

?>