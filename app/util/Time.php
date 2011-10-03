<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\util;

class Time extends \lithium\core\StaticObject {
	
	const SECOND = 1;
	
	const MINUTE = 60;
	
	const HOUR = 3600;
	
	const DAY = 86400;
	
	const WEEK = 604800;
	
	public static function period($seconds, array $options = array()) {
		$options += array(
			'long' => false,
			'periods' => array('week', 'day', 'hour', 'minute')
		);
		extract($options);
		
		if($seconds < static::MINUTE) {
			$periods = array('second');
		}
		
		$values = array_fill_keys($periods, 0);
		extract($values);
		while($var = array_shift($periods)) {	
			$const = constant(__CLASS__ . '::' . strtoupper($var));
			while ($seconds > $const) {
				$count = floor($seconds / $const);
				$$var = $count;
				$seconds = $seconds - ($count * $const);
			}
		}
		$period = array_filter(compact(array_keys($values)));
		$output = array();
		foreach ($period as $p => $t) {
			if (!$long) {
				$p = substr($p, 0, 1);
			} else if ($t > 1) {
				$p .= 's';
			}
			$output[] = "{$t}{$p}";
		}
		return join(' ', $output);
	}
	
	public static function hours($seconds, $round = 'ceil') {
		return number_format($seconds / static::HOUR, 1, '.', '');
	}
}
?>