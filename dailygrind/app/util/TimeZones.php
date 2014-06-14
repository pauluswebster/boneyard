<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\util;

use \DateTimeZone;

class TimeZones extends \lithium\core\StaticObject {

	/**
	 * Get a list of PHP timezones
	 *
	 * @param boolean $grouped
	 */
	public static function get($group = true){
		static $regions = array();
		static $grouped = array();
		if (empty($regions)) {
			$masks = array(
			    'Africa' => DateTimeZone::AFRICA,
			    'America' => DateTimeZone::AMERICA,
			    'Antarctica' => DateTimeZone::ANTARCTICA,
			    'Asia' => DateTimeZone::ASIA,
			    'Atlantic' => DateTimeZone::ATLANTIC,
			    'Europe' => DateTimeZone::EUROPE,
			    'Indian' => DateTimeZone::INDIAN,
			    'Pacific' => DateTimeZone::PACIFIC
			);
			foreach ($masks as $name => $mask) {
		    	$region = DateTimeZone::listIdentifiers($mask);
		    	$grouped[$name] = array_combine($region, $region);
		    	array_walk($grouped[$name], function(&$tz) use ($name) {
		    		$tz = str_replace("{$name}/", '', $tz);
		    	});
		    	$regions = array_merge($regions, $region);
			}
		}
		return $group ? $grouped : $regions;
	}
}

?>