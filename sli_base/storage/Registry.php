<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\storage;

/**
 * The `Registry` class provides global storage for configuration settings,
 * convenient loading/saving via `sli_base\storage\Configuration` and data
 * access with `sli_base\storage\ArrayStore`;
 *
 * @see sli_base\storage\ArrayStore
 * @see sli_base\storage\Configuration
 */
class Registry extends \lithium\core\StaticObject {

	/**
	 * Class dependancies
	 *
	 * @var array
	 */
	protected static $_classes = array(
		'storage' => 'sli_base\storage\ArrayStore',
		'source' => 'sli_base\storage\Source'
	);

	/**
	 * Registry Data Storage
	 *
	 * @var array
	 */
	protected static $_storage = null;

	/**
	 * Overloaded static calls are passed to storage instance
	 *
	 * @see sli_base\storage\ArrayStore
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public static function __callStatic($method, $params) {
		return static::storage()->invokeMethod($method, $params);
	}

	/**
	 * Delete data at registry path, returning previous value
	 *
	 * @param mixed $path
	 * @return mixed
	 */
	public static function flush($path = null){
		$storage =& static::storage();
		$data = $storage->get($path);
		$storage->delete($path);
		return $data;
	}

	/**
	 * Save registry values with Configuration class
	 *
	 * @param mixed $path configuration path to read
	 * @param mixed $options configuration options
	 * @param mixed $registry string registry path to set data to | array keys:
	 * 		`path`: string registry path to set data to
	 * 		`delete`: boolean delete registry data from path on successful save
	 * @return boolean result of Configuration::write($path, $options)
	 */
	public static function save($path, $options = array(), $registry = array()){
		if (!is_array($registry)) {
			$registry = array('path' => $registry);
		}
		$registry += array('path' => null, 'delete' => false);
		if (!is_array($options)) {
			$options = isset($options) ? array('name' => $options) : array();
		}
		$source = static::$_classes['source'];
		$storage =& static::storage();
		$data = $storage->get($registry['path']);
		$save = $source::write($path, $data, $options);
		if ($save && $registry['delete']) {
			$storage->delete($registry['path']);
		}
		return $save;
	}

	/**
	 * Load registry values from Configuration class
	 *
	 * @param mixed $path configuration path to read
	 * @param mixed $options configuration options
	 * @param mixed $registry string registry path to set data to | array keys:
	 * 		`path`: string registry path to set data to
	 * 		`write`: boolean store data on successful load
	 * 		`merge`: boolean merge data on successful load
	 * @return mixed result of Configuration::read($path, $options)
	 */
	public static function load($path, $options = array(), $registry = array()){
		if (!is_array($registry)) {
			$registry = array('path' => $registry);
		}
		$registry += array('path' => null, 'write' => true, 'merge' => false);
		if (!is_array($options)) {
			$options = isset($options) ? array('name' => $options) : array();
		}
		$source = static::$_classes['source'];
		$data = $source::read($path, $options);
		if (isset($data) && $registry['write']) {
			$storage =& static::storage();
			$storage->set($registry['path'], $data, $registry['merge']);
		}
		return $data;
	}

	/**
	 * Get ArrayStore instance used to store registry data
	 */
	public static function &storage() {
		if (!isset(static::$_storage)) {
			$storage = static::$_classes['storage'];
			static::$_storage = new $storage();
		}
		return static::$_storage;
	}
}
?>