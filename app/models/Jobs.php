<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace app\models;

use sli_util\storage\Registry;
use app\util\TimeZones;
use app\util\CurrencyConverter;
use app\security\User;

class Jobs extends WorkUnit {

	public static function fee($record, $currency = null) {
		if (!isset($currency)) {
			$currency = User::instance('default')->currency();
		}
		if (!$currency) {
			$fee = $record->fee;
		} else {
			$base = $record->currency;
			$to = $currency;
			$fee = CurrencyRates::convert($base, $to, $record->fee);
		}
		if (!$record->fixed) {
			$hours = static::hours($record);
			if ($hours < 1) $hours = 1;
			$fee = $fee * $hours;
		}
		return number_format($fee, 2, '.', '');
	}

	public static function fees($record, $currency = null) {
		$user = static::fee($record, $currency);
		$fees = '$' . $user;
		$job = static::fee($record, false);
		if ($user != $job) {
			$fees.= " [\${$job} {$record->currency}]";
		}
		return $fees;
	}

	public static function rate($record, $currency = null, $raw = false) {
		if (empty($record->started) || !($fee = static::fee($record, $currency))) {
			return 'n/a';
		}
		$hours = static::hours($record);
		if ($hours < 1) $hours = 1;
		$rate = number_format($fee/$hours, 2, '.', '');
		if (!$raw) {
			$rate = "{$hours}h @ \${$rate}";
			if (!$record->fixed) {
				$job = static::fee($record, false);
				if ($fee != $job) {
					$job = number_format($record->fee, 2, '.', '');
					$rate.= " [\${$job} {$record->currency}]";
				}
			} else {
				//converted fx of hourly rate
			}
		}
		return $rate;
	}

	public static function tasks($record) {
		if (is_object($record)) {
			if (isset($record->taskCount)) {
				return $record->taskCount;
			}
			$id = $record->id;
		} else {
			$id = $record;
		}
		$count = Tasks::count(array('conditions' => array('job_id' => $id)));
		if (is_object($record)) {
			$record->taskCount = $count;
		}
		return $count;
	}

	public static function getScaffoldFormFields(){
		$user = User::instance('default');
		$fields = array(
			'title',
			'reference',
			'description' => array('type' => 'textarea'),
			'fee',
			'fixed'=> array(
				'type' => 'select',
				'list' => array(
					1 => 'Fixed Fee',
					0 => 'Hourly Fee'
				),
				'label' => 'Fee Basis'
			),
			'currency' => array(
				'type' => 'select',
				'list' => array(
					'All Currencies' => CurrencyConverter::currencies(),
					'My Currencies' => $user->currencies()
				)
			),
			'due' => array(
				'class' => 'date-picker',
				'data-format' => Registry::get('app.date.js-long')
			),
			'timezone' => array(
				'type' => 'select',
				'list' => TimeZones::get() + array(
					'My TimeZones' => $user->timezones()
				)
			)
		);
		return array(
			'Job' => compact('fields')
		);
	}

	protected static function _applyFilters() {
		parent::_applyFilters();
		static::applyFilter('create', function($self, $params, $chain){
			if (empty($params['data']['currency'])) {
				$params['data']['currency'] = User::instance('default')->currency();
			}
			return $chain->next($self, $params, $chain);
		});
	}
}
?>