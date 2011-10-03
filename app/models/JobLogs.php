<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\models;

class JobLogs extends \lithium\data\Model {
	
	public $belongsTo = array('Users', 'Jobs');

	public static function start($user_id, $job_id){
		if ($current = static::current($user_id)) {
			if ($current->job_id == $job_id) {
				return $current;
			}
			static::stop($current);
		}
		$job = Jobs::first(array('conditions' => array('id' => $job_id)));
		if (!$job) {
			return false;
		}
		$start = time();
		if (empty($job->started)) {
			$job->started = $start;
			$job->save();
		}
		$log = static::create(compact('user_id', 'job_id', 'start'));
		$log->save();
		return static::current($user_id);
	}

	public static function current($user_id) {
		return static::first(array(
			'conditions' => array(
				'JobLogs.user_id' => $user_id,
				'JobLogs.end' => 0
			),
			'with' => array('Jobs')
		));		
	}
	
	public static function timeSpent($job_id) {
		$result = static::first(array(
			'conditions' => array(
				'JobLogs.job_id' => $job_id,
				'JobLogs.end' => array('>' => 0)
			),
			'fields' => array(
				'start',
				'end',
				'user_id',
				'SUM(end - start) as spent'
			)
		));		
		$seconds = 1;
		if ($result) {
			$seconds = $result->spent; 
		}
		$progress = static::first(array(
			'conditions' => array(
				'JobLogs.job_id' => $job_id,
				'JobLogs.end' => 0
			)
		));
		if ($progress) {
			$seconds += time() - $progress->start;
		}
		return $seconds;
	}
	
	public static function stop($user_id) {
		if ($job = static::current($user_id)) {
			$job->end = time();
			if ($job->save()) {
				return $job;
			}
		}
	}
	
}

?>