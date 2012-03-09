<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace app\models;

use app\util\CurrencyConverter;
use sli_base\util\Behaviors;

class CurrencyRates extends \lithium\data\Model {

	public static function __init() {
		Behaviors::apply(__CLASS__, array(
			'Timestamped' => array(
				'update' => 'modified',
				'format' => 'U'
			)
		));
		parent::__init();
	}

	/**
	 * Fetch the current rate for all existing rate's in use and update records
	 *
	 * @return integer number of rate records updated
	 */
	public static function fetchAllRates() {
		$updated = 0;
		$rates = static::all();
		if($rates->count() > 0) {
			do {
				$rate = $rates->current();
				if ($update = CurrencyConverter::rate($rate->base, $rate->to, false)) {
					$rate->rate = $update;
					if($rate->save()) {
						$updated++;
					}
				}
			} while($rates->next());
		}
		CurrencyConverter::currencies(false);
		return $updated;
	}

	public static function convert($base , $to, $amount = 1, $rate = null) {
		if ($base == $to) {
			$rate = 1;
		}
		if (!$rate) {
			$record = static::first(array(
				'conditions' => compact('base', 'to')
			));
			if ($record && $record->modified > strtotime('-12 hours')) {
				$rate = $record->rate;
			} else {
				if ($update = CurrencyConverter::rate($base, $to, false)) {
					$rate = $update;
					if (!$record) {
						$record = static::create(compact('base', 'to', 'rate'));
					} else {
						$record->rate = $rate;
					}
					$record->save();
				}
			}
		}
		return CurrencyConverter::convert($base, $to, $amount, $rate);
	}
}

?>