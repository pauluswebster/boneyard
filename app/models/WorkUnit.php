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
use app\util\Time;
use app\security\User;

class WorkUnit extends \lithium\data\Model {

	protected static $_statuses = array(
		'current' => array('completed' => 0),
		'new' => array('started' => 0),
		'in_progress' => array('started' => array('>' => 0), 'completed' => 0),
		'completed' => array('completed' => array('>' => 0))
	);

	public static function __init() {
		static::$_baseClasses[__CLASS__] = true;
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

	public static function time($record, $string = false) {
		$time = static::timeSpent($record);
		return !$string ? $time : Time::period($time);
	}

	public static function timeSpent($record) {
		if (isset($record->timeSpent)) {
			return $record->timeSpent;
		}
		$key = 'job_id';
		if (get_called_class() == 'app\models\Tasks') {
			$key = 'task_id';
		}
		return JobLogs::timeSpent(array($key => $record->id));
	}

	public static function due($record, $raw = false) {
		if (isset($record->__due)) {
			return $raw ? $record->__due : $record->due;
		}
	}

	protected static function _applyFilters() {
		$user = User::instance('default');
		$class = get_called_class();
		Behaviors::apply($class, array(
			'DateTimeZoned' => array(
				'fields' => array(
					'due' => array(
						'timezone' => $user->timezone(),
						'timezone_field' => 'timezone',
						'format' => Registry::get('app.date.long'),
						'filter' => array('find', 'save')
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

	/**
	 * @deprecated
	 */
	public static function timeString($record) {
		trigger_error(__METHOD__, E_USER_DEPRECATED);
		return static::time($record, true);
	}
}
?>