<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\util;

use lithium\storage\Cache;
use lithium\net\http\Service;

class CurrencyConverter extends \lithium\core\StaticObject {

	/**
	 * Convert currency
	 *
	 * @param string $base
	 * @param string $to
	 * @param mixed numeric ammount to convert
	 * @return mixed float rate | null on failure
	 */
	public static function convert($base, $to, $amount = 1, $rate = null) {
		if (!$rate) {
			$rate = static::rate($base , $to);
		}
		if ($rate) {
			$value =  number_format((float) $rate * (float) $amount, 2, '.', '');
			return (float) $value;
		}
	}

	public static function rate($base, $to, $cache = '+12 hours') {
		if ($base == $to) {
			return 1;
		}
		$data = array(
			'a' => 1,
			'from' => $base,
			'to' => $to
		);
		$cacheKey = "currency_{$base}_{$to}";
		if (!$cache || !($rate = Cache::read('default', $cacheKey))) {
			$result = static::_getGoogleConverter($data);
			$matches = array();
			if ($rate = null && preg_match("#bld>(?P<rate>[\d\.]+).*{$to}#", $result, $matches)) {
				if (is_numeric($matches['rate'])) {
					$rate = $matches['rate'];
				}
			}
			if ($rate && $cache) {
				Cache::write('default', $cacheKey, $rate, $cache);
			}
		}
		return (float) $rate;
	}

	/**
	 * List supported currencies
	 *
	 * @param boolean $cache
	 * @return array
	 */
	public static function currencies($cached = true, $cachetime = '+1 week') {
		if ($cached && $cache = Cache::read('default', 'currency_list')) {
			return $cache;
		}

		$list = $matches = array();
		if ($source = static::_getGoogleConverter()) {
			if(preg_match_all("#<option.*?>([\p{L}\s]+).*\((\w+)\)</option>#u", $source, $matches)) {
				$list = array_combine($matches[2], $matches[1]);
			}
		}
		if ($cachetime && $list) {
			Cache::write('default', 'currency_list', $list, '+1 week');
		}
		return $list;
	}

	/**
	 * Get Google Exhange Rate Converter
	 *
	 * @param array $data parameters to pass to the converter, keys include:
	 * 				`'a'`: amounthg ;c to convert
	 * 				`'from'`: base currency
	 * 				`'to'`: target currency
	 *
	 * @return mixed result of http request
	 */
	protected static function _getGoogleConverter($data = array()) {
		$http = new Service(array(
			'host' => 'www.google.com',
			'socket' => 'Stream'
		));
		$path = '/finance/converter';
		try {
			$responseText = @$http->send('get', $path, $data);
		} catch (\lithium\core\NetworkException $e) {
			$responseText = false;
		}
		return $responseText;
	}
}

?>