<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

use app\util\Time;

class JobLogs extends \lithium\data\Model {

	public $belongsTo = array('Users', 'Jobs', 'Tasks');

	public static function start($user_id, $job_id, $task_id = null){
		if ($current = static::current($user_id)) {
			if ($current->job_id == $job_id) {
				if (!$task_id || $current->task_id == $task_id) {
					return $current;
				}
			}
			static::stop($current);
		}
		$job = Jobs::first(array('conditions' => array('id' => $job_id)));
		if (!$job) {
			$class = get_called_class();
			throw new Exception(sprintf('Job not found in %s', $class));
		}
		if ($task_id) {
			$task = Tasks::first(array('conditions' => array('id' => $task_id)));
			if (!$task) {
				$class = get_called_class();
				throw new Exception(sprintf('Job not found in %s', $class));
			}
		}

		$start = time();
		if (empty($job->started)) {
			$job->started = $start;
			$job->save();
		}
		if (!empty($task) && empty($task->started)) {
			$task->started = $start;
			$task->save();
		}
		$log = static::create(compact('user_id', 'job_id', 'task_id', 'start'));
		$log->save();
		return static::current($user_id);
	}

	public static function stop($user_id) {
		if ($active = static::current($user_id)) {
			$active->end = time();
			if ($active->save()) {
				return $active;
			}
		}
	}

	public static function current($user_id) {
		$record = static::first(array(
			'conditions' => array(
				'JobLogs.user_id' => $user_id,
				'JobLogs.end' => 0
			),
		));
		if ($record) {
			$record->job = Jobs::first($record->job_id);
			$record->task = !$record->task_id ? null :Tasks::first($record->task_id);
		}
		return $record;
	}

	public static function active($key) {
		$record = static::first(array(
			'conditions' => array(
				'JobLogs.id' => $key['id'],
				'JobLogs.end' => 0
			),
		));
		if ($record) {
			$record->job = Jobs::first($record->job_id);
			$record->task = !$record->task_id ? null :Tasks::first($record->task_id);
		}
		return $record;
	}

	public static function timeSpent($scope, $string = false, $current = false) {
		if (!is_array($scope)) {
			$scope = array('job_id' => $job_id);
		}
		$seconds = 0;
		if (!$current) {
			$result = static::first(array(
				'conditions' => array(
					'end' => ($current ? 0 : array('>' => 0))
				) + $scope,
				'fields' => array(
					'start',
					'end',
					'user_id',
					'SUM(end - start) as spent'
				)
			));
			if ($result) {
				$seconds = $result->spent;
			}
		}
		$progress = static::first(array(
			'conditions' => array(
				'end' => 0
			)  + $scope
		));
		if ($progress) {
			$seconds += time() - $progress->start;
		}
		return $string ? Time::period($seconds) : $seconds;
	}

	public static function unit($record) {
		if (isset($record->task)) {
			return $record->task;
		} else {
			return $record->job;
		}
	}

	/**
	 * @deprecated
	 */
	public static function time($record, $string = false) {
		trigger_error(__METHOD__, E_USER_DEPRECATED);
		$progress = static::first(array(
			'conditions' => array(
				'JobLogs.job_id' => $record->job_id,
				'JobLogs.end' => 0
			)
		));
		if ($progress) {
			$seconds += time() - $progress->start;
		}
		if (empty($seconds)) {
			$seconds = $string ? 60 : 0;
		}
		return $string ? Time::period($seconds) : $seconds;
	}
}

?>