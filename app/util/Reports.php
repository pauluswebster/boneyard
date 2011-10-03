<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\util;

use app\models\Jobs;
use app\models\JobLogs;
use app\security\User;
use lithium\util\Inflector;

class Reports extends \lithium\core\StaticObject {
	
	protected static $_reports = array(
		'completed' => 'Completed'
	);
	
	public static function available($report = null) {
		if ($report) {
			return array_key_exists($report, static::$_reports);
		}
		return static::$_reports;
	}
	
	public static function run($report, $params = array()) {
		if (!static::available($report)) {
			return;
		}
		$method = "_$report";
		$data = array(
			'params' => $params,
			'results' => static::$method($params)
		);
		return $data;		
	}
	
	protected static function _result() {
		$resultSchema = array('fields', 'data', 'totals');
		return array_fill_keys($resultSchema, array());
	}
	
	public static function _completed($params) {
		$result = static::_result();
		$result['fields'] = array(
			'period' => '',
			'jobs' => 'Jobs Completed',
			'time' => 'Hours Worked',
			'earnings' => 'Est. Total Earnings',
			'rate' => 'Avg. Hourly Rate'
		);
		
		$periods =  static::_periods();
		foreach ($periods as $period => $dates) {
			$result['data'][] = array(
				'period' => Inflector::humanize($period),
			) + static::_summarizeCompleted($dates + $params);
		}
		
		return array(
			'Completed Job Summary' => $result
		);
	}
	
	protected static function _summarizeCompleted($params = array()) {
		$completed = static::_completedJobs($params);
		$jobs = count($completed);
		$time = $earnings = $rate = null;
		$rates = array();
		if ($jobs > 0) {
			foreach ($completed as $job) {
				$time += $job->time();
				$earnings += $job->fee();
			}
			if ($time) {
				$time = Time::hours($time);
				$rate = number_format($earnings/$time, 2, '.', '');
			}
		}
		return compact('jobs', 'time', 'earnings', 'rate');
	}
	
	protected static function _completedJobs($params) {
		extract($params);
		$start = isset($start) ? $start : 0;
		$conditions = array(
			'and' => array(
				array(
					'completed' => array(
						'>' => (int) $start
					)
				)
			)
		);
		if (isset($end)) {
			$conditions['and'][] = array('completed' => array('<' => (int) $end));
		}
		if (isset($user_id)) {
			$conditions['and'][] = compact('user_id');
		}
		return Jobs::all(compact('conditions'));
	}
	
	protected static function _periods() {
		$user =& User::instance('default');
		$periods = array();
		
		$tz = new \DateTimeZone($user->timezone());
		$dateTime = new \DateTime('now', $tz);
		
		//today : since midnight
		$today = clone $dateTime;
		$today->setTime('00','00');
		$periods['today'] = array(
			'start' => $today->getTimestamp()
		);
		
		//thisWeek : current week from monday
		$thisWeek = clone $today;
		while($thisWeek->format('N') > 1) {
			$thisWeek->modify('-1 day');
		}
		$periods['this_week'] = array(
			'start' => $thisWeek->getTimestamp()
		);
		
		//last week : previous week
		$lastWeek = clone $thisWeek;
		$lastWeek->modify('-1 week');
		$periods['last_week'] = array(
			'start' => $lastWeek->getTimestamp(),
			'end' => $thisWeek->getTimestamp()
		);
		
		//thisMonth : current calender month
		$thisMonth = clone $today;
		while($thisMonth->format('j') > 1) {
			$thisMonth->modify('-1 day');
		}
		$periods['this_month'] = array(
			'start' => $thisMonth->getTimestamp()
		);
		
		//lastMonths : previous calendar month
		$lastMonths = clone $thisMonth;
		$lastMonths->modify('-1 month');
		$periods['last_month'] = array(
			'start' => $lastMonths->getTimestamp(),
			'end' => $thisMonth->getTimestamp()
		);
		
		//thisYear : current calendar year
		$thisYear = clone $today;
		while($thisYear->format('n') > 1) {
			$thisYear->modify('-1 month');
		}
		while($thisYear->format('j') > 1) {
			$thisYear->modify('-1 day');
		}
		$periods['this_year'] = array(
			'start' => $thisYear->getTimestamp(),
		);
		
		$taxYear = clone $today;
		while($taxYear->format('n') > 4) {
			$taxYear->modify('-1 month');
		}
		while($taxYear->format('j') > 1) {
			$taxYear->modify('-1 day');
		}
		$periods['tax_year'] = array(
			'start' => $taxYear->getTimestamp(),
		);
		
		$lastYear = clone $thisYear;
		$lastYear->modify('-1 year');
		$periods['last_year'] = array(
			'start' => $lastYear->getTimestamp(),
			'end' => $thisYear->getTimestamp()
		);
		
		$lastTaxYear = clone $taxYear;
		$lastTaxYear->modify('-1 year');
		$periods['last_tax_year'] = array(
			'start' => $lastTaxYear->getTimestamp(),
			'end' => $taxYear->getTimestamp()
		);
		
		return $periods;
	}	
}
?>