<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace app\models;

use sli_util\storage\Registry;
use sli_filters\util\Behaviors;
use app\models\JobLogs;
use app\models\CurrencyRates;
use app\util\TimeZones;
use app\util\Time;
use app\util\CurrencyConverter;
use app\security\User;

class Jobs extends \lithium\data\Model {

	protected static $_statuses = array(
		'current' => array('completed' => 0),
		'new' => array('started' => 0),
		'in_progress' => array('started' => array('>' => 0), 'completed' => 0),
		'completed' => array('completed' => array('>' => 0))
	);

	public static function __init() {
		static::_applyFilters();
		parent::__init();
	}

	public static function statuses($status = null) {
		if ($status && isset(static::$_statuses[$status])) {
			return static::$_statuses[$status];
		} else {
			return static::$_statuses;
		}
	}

	public static function status($record) {
		$status = 'in_progress';
		if(empty($record->started)):
			$status = 'new';
		elseif(!empty($record->completed)):
			$status = 'complete';
		endif;
		return $status;
	}

	public static function hours($record) {
		$spent = static::time($record);
		return Time::hours($spent);
	}

	public static function time($record) {
		return JobLogs::timeSpent($record->id);
	}

	public static function timeString($record) {
		return Time::period(static::time($record));
	}

	public static function fee($record, $currency = null) {
		if (!isset($currency)) {
			$currency = User::instance('default')->currency();
		} else if (!$currency) {
			return $record->fee;
		}
		$base = $record->currency;
		$to = $currency;
		$fee = CurrencyRates::convert($base, $to, $record->fee);
		return number_format($fee, 2, '.', '');
	}

	public static function fees($record, $currency = null) {
		$user = static::fee($record, $currency);
		$job = number_format($record->fee, 2, '.', '');
		$fees = '$' . $user;
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
		return $raw ? $rate : "{$hours}h @ \${$rate}";
	}

	public static function getScaffoldFormFields(){
		$user = User::instance('default');
		$fields = array(
			'title',
			'reference',
			'description' => array('type' => 'textarea'),
			'fee',
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
		$user = User::instance('default');

		Behaviors::apply(__CLASS__, array(
			'DateTimeZoned' => array(
				'fields' => array(
					'due' => array(
						'timezone' => $user->timezone(),
						'timezone_field' => 'timezone',
						'format' => Registry::get('app.date.long')
					)
				)
			),
			'Timestamped' => array(
				'update' => 'modified',
				'format' => 'U'
			)
		));

		static::applyFilter('create', function($self, $params, $chain) use ($user){
			if (empty($params['data']['timezone'])) {
				$params['data']['timezone'] = $user->timezone();
			}
			if (empty($params['data']['currency'])) {
				$params['data']['currency'] = $user->currency();
			}
			return $chain->next($self, $params, $chain);
		});

		static::applyFilter('save', function($self, $params, $chain) use ($user){
			$data =& $params['data'];
			$entity =& $params['entity'];
			if (empty($entity->user_id) && empty($data['user_id'])) {
				$data['user_id'] = $user->id;
			}
			return $chain->next($self, $params, $chain);
		});

		static::applyFilter('find', function($self, $params, $chain){
			if (isset($params['options']['conditions']['status'])) {
				$conditions =& $params['options']['conditions'];
				$self::invokeMethod('_applyStatusConditions', array(&$conditions));
			}
			return $chain->next($self, $params, $chain);
		});
	}

	protected static function _applyStatusConditions(&$conditions) {
		if (!($status = static::statuses($conditions['status']))) {
			$status = static::statuses('current');
		}
		unset($conditions['status']);
		$conditions = $status + $conditions;
	}

}
?>