<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\extensions\behavior\data\model;

/**
 * Format unix timestamped fields into timezone aware string date formats
 */
class DateTimeZoned extends \sli_filters\data\model\behavior\Modified {

	protected static $_fieldDefaults = array(
		'timezone_field' => null,
		'timezone' => null,
		'format' => 'Y-m-d H:i:s',
		'rawFormat' => 'U'
	);

	protected static function _apply($class, &$settings) {
		parent::_apply($class, $settings);
		$class = get_called_class();
		$invoke = function($method, $field, $value, &$data, $config) use ($class){
			$args = array($field, $value, &$data, $config);
			return $class::invokeMethod($method, $args);
		};
		foreach ($settings['fields'] as $field => &$config) {
			$config = $config + static::$_fieldDefaults;
			$args = array('_export', $field, '{:field}', '{:data}', $config);
			$methods['create'] = $methods['find'] = array(
				'call' => $invoke,
				'args' => $args
			);
			$methods['save'] = array(
				'call' => $invoke,
				'args' => array('_prepare') + $args
			);
			$config += $methods;
		}
	}

	protected static function _prepare($field, $value, $data, $config) {
		$timezone = static::_timezone($data, $config);
		$dateTime = \DateTime::createFromFormat($config['format'], $value, $timezone);
		$dateTime->setTimezone($timezone);
		return $dateTime->format($config['rawFormat']);
	}

	protected static function _export($field, $value, &$data, $config) {
		$value = $value ?: time();
		$timezone = static::_timezone($data, $config);
		$dateTime = \DateTime::createFromFormat($config['rawFormat'], $value, $timezone);
		$dateTime->setTimezone($timezone);
		$data["_{$field}"] = $dateTime;
		$data["__{$field}"] = $value;
		return $dateTime->format($config['format']);
	}

	protected static function _timezone($data, $config) {
		$timezone = date_default_timezone_get();
		$field = $config['timezone_field'];
		if ($field && !empty($data[$field])) {
			$timezone = $data[$field];
		} elseif($config['timezone']) {
			$timezone = is_callable($config['timezone']) ? $config['timezone']() : $config['timezone'];
		}
		return new \DateTimeZone($timezone);
	}
}

?>