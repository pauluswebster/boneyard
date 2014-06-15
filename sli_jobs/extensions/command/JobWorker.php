<?php

namespace sli_jobs\extensions\command;

use lithium\core\Libraries;
use lithium\console\Dispatcher;

class JobWorker extends \lithium\console\Command {

	protected $_queues = array(
		'default' => array()
	);

	protected $_defaults = array(
		'model' => 'sli_jobs\models\Jobs',
		'director' => 'sli_jobs\core\Director',
		'conditions' => array(),//@todo
		'workers' => 5,
		'ttl' => 1,
		'delay' => 1,
		'jobLimit' => null,
		'processLimit' => null,
		'inactiveLimit' => null,
		'timeLimit' => 600,
		'scale' => null,//@todo
	);

	protected $_queue = array();

	protected $_worker;

	protected $_pid;

	protected $_lastJobTime = 0;

	protected $_startTime = 0;

	protected $_processCount = 0;

	protected $_jobCount = 0;

	public function __construct(array $config = array()) {
		parent::__construct($config);
		foreach ($this->_queues as $queue => &$settings) {
			$settings += $this->_defaults;
		}
	}

	public function run() {
		extract($this->request->params);
		if (isset($queue, $worker)) {
			if (!isset($this->_queues[$queue])) {
				$this->error("{$queue} does not exist", 'error');
				return;
			}
			$this->_worker = $worker;
			$this->_queue = array('name' => $queue) + $this->_queues[$queue];
			$this->_pid = getmypid();
			$this->_startWork();
		}
	}

	protected function _startWork() {
		$this->out("{$this->_queue['name']} worker #{$this->_worker} starting with pid #{$this->_pid}");
		$this->_lastJobTime = $this->_startTime = time();
		while($this->_canWork() && $this->_doWork()) {
			$this->_processCount++;
		}
	}

	protected function _stopWork($message = null, $exit = 0) {
		if (!is_null($message)) {
			$message = "{$this->_queue['name']} worker #{$this->_worker} stopping: $message";
			if ($exit > 0) {
				$this->error($message, 'error');
			} else {
				$this->out($message);
			}
		}
		$this->out("{$this->_queue['name']} worker #{$this->_worker} stopped");
		$this->_stop($exit);
	}

	protected function _doWork() {
		$director = $this->_queue['director'];
		$director::trackActivity($this->_queue['name'], $this->_worker);
		$start = time();
		if ($job = $this->_getWork()) {
			$this->_jobCount++;
			$this->_lastJobTime = $start;
			$this->_dispatchJob($job);
		}
		if ($this->_queue['delay']) {
			$wait = $start + $this->_queue['delay'] - time();
			if ($wait > 0) {
				$this->out("{$this->_queue['name']} worker #{$this->_worker} sleeping for {$wait}");
				sleep($wait);
			}
		}
		if (time() > $start) {
			$director::trackActivity($this->_queue['name'], $this->_worker);
		}
		return true;
	}

	protected function _canWork() {
		if ($this->_queue['jobLimit']) {
			if ($this->_jobCount >= $this->_queue['jobLimit']) {
				$this->_stopWork('job limit reached');
			}
		}
		if ($this->_queue['processLimit']) {
			if ($this->_processCount >= $this->_queue['processLimit']) {
				$this->_stopWork('process limit reached');
			}
		}
		if ($this->_queue['inactiveLimit']) {
			$inactive = time() - $this->_lastJobTime;
			if ($inactive >= $this->_queue['inactiveLimit']) {
				$this->_stopWork('inactive limit reached');
			}
		}
		if ($this->_queue['timeLimit']) {
			$active = time() - $this->_startTime;
			if ($active >= $this->_queue['timeLimit']) {
				$this->_stopWork('time limit reached');
			}
		}
		$director = $this->_queue['director'];
		if ($director::stopSent($this->_queue['name'], $this->_worker)) {
			$this->_stopWork('stop signal received');
		}
		return true;
	}

	protected function _getWork() {
		$model = $this->_queue['model'];

// 		$model::queue($this->_queue['name'], 'Job');//@todo remove

		$workerSignature = gethostname() . "::{$this->_worker}::{$this->_pid}";
		return $model::dequeue($this->_queue['name'], $workerSignature);
	}

	protected function _dispatchJob($job) {
		$_queue = $this->_queue['name'];
		$_worker = $this->_worker;
		$_job = implode(':', $job->key());
		$args = $job->args ? unserialize($job->args) : array();
		$args+= array(
			'queue' => $this->_queue['name'],
			'worker' => $job->worker,
			'job' => $_job
		);
		$command = array($job->command, $job->action) + $args;
		$job->running()->save();
		$this->out("{$this->_queue['name']} worker #{$this->_worker} starting job #{$_job}");
		if ($this->_execute($command, false)) {
			$this->out("{$this->_queue['name']} worker #{$this->_worker} completed job #{$_job}");
			$job->completed();
		} else {
			$this->out("{$this->_queue['name']} worker #{$this->_worker} failed job #{$_job}");
			$atr = false;//@todo attempt limit reach
			if ($atr) {
				$job->failed();
			} else {
				$job->error();
			}
		}
		$job->save();
	}

	protected function _execute($args, $background = false, $output = '', $error = null) {
		foreach ($args as $key => &$arg) {
			if (!is_numeric($key)) {
				$arg = "--{$key}={$arg}";
			}
			$arg = escapeshellarg($arg);
		}
		escapeshellcmd($command = implode(' ', $args));
		$file = $this->request->env('PHP_SELF');
		$cmd = "php -f {$file} {$command}";
		if ($background) {
			$output = $output ?: '/dev/null';
			$error = $error ?: '/dev/null';
			if ($error === true) {
				//both to one
				exec(sprintf("%s > %s 2>&1 &", $cmd, $output));
			} else {
				//seperate
				exec(sprintf("%s > %s 2> %s &", $cmd, $output, $error));
			}
			return true;
		} else {
			exec($cmd, $_output, $_exit);
			$out = $_exit > 0 ? 'error' : 'out';
			if ($_output && ${$out} !== false) {
				if (is_callable(${$out})) {
					${$out}($_output, $_exit);
				} else {
					$this->{$out}($_output);
				}
			}
			if ($_exit > 0) {
				return false;
			} else {
				return true;
			}
		}
	}
}