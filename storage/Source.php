<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\storage;

/**
 * The `Source` class provides standard access methods for reading,
 * writing & deleting data from various sources & in various formats, its
 * intended use is to simplify the reading and writing of configuration data.
 */
class Source extends \lithium\core\Adaptable {

	/**
	 * Stores configurations for cache adapters
	 *
	 * @var object Collection of cache configurations
	 */
	protected static $_configurations = array(
		'default' => array(
			array(
				'adapter' => 'File',
				'strategies' => array(
					'Export'
				),
				'filters' => array()
			)
		)
	);

	/**
	 * Libraries::locate() compatible path to adapters for this class.
	 *
	 * @var string Dot-delimited path.
	 */
	protected static $_adapters = 'adapter.storage.source';

	/**
	 * Libraries::locate() compatible path to strategies for this class.
	 *
	 * @var string Dot-delimited path.
	 */
	protected static $_strategies = array(
		'strategy.storage.source',
		'strategy.storage.cache'
	);

	/**
	 * Read configuration data from source
	 *
	 * @param mixed $path
	 * @param array $options
	 * @return mixed
	 */
	public static function read($path, array $options = array()){
		$defaults = array('name' => 'default', 'strategies' => true);
		$options += $defaults;
		extract($options);
		$settings = static::_config($name);
		$method = static::adapter($name)->read($path, $options);
		$params = compact('path', 'options');
		$filters = $settings['filters'];
		$result = static::_filter(__FUNCTION__, $params, $method, $filters);
		if ($strategies) {
			$_options = array('path' => $path, 'mode' => 'LIFO', 'class' => __CLASS__);
			$options += $_options;
			$result = static::applyStrategies(__FUNCTION__, $name, $result, $options);
		}
		return $result;
	}

	/**
	 * Write configuration data to source
	 *
	 * @param mixed $path
	 * @param mixed $data
	 * @param array $options
	 * @return mixed
	 */
	public static function write($path, $data, array $options = array()){
		$defaults = array('name' => 'default', 'strategies' => true);
		$options += $defaults;
		extract($options);

		if ($strategies) {
			$_options = array('path' => $path, 'class' => __CLASS__) + $options;
			$data = static::applyStrategies(__FUNCTION__, $name, $data, $_options);
		}

		$settings = static::_config($name);
		$method = static::adapter($name)->write($path, $data, $options);
		$params = compact('path', 'data', 'options');
		$filters = $settings['filters'];
		return static::_filter(__FUNCTION__, $params, $method, $filters);
	}

	/**
	 * Delete configuration data from source
	 *
	 * @param mixed $path
	 * @param array $options
	 * @return boolean
	 */
	public static function delete($path, array $options = array()){
		$defaults = array('name' => 'default');
		$options += $defaults;
		extract($options);
		$settings = static::_config($name);
		$method = static::adapter($name)->delete($path, $options);
		$params = compact('path', 'options');
		$filters = $settings['filters'];
		$result = (boolean) static::_filter(__FUNCTION__, $params, $method, $filters);
	}
}