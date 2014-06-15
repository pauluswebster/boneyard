<?php

namespace sli_jobs\extensions\command;

use lithium\core\Libraries;

class JobQueue extends JobWorker {

	public function run($queue = null, $force = false) {
		$queues = array();
		if (!empty($queue)) {
			if (isset($this->_queues[$queue])) {
				$queues[$queue] = $this->_queues[$queue];
			} else {
				$this->error("{$queue} does not exist", 'error');
			}
		} else {
			$queues = $this->_queues;
		}
		foreach ($queues as $queue => $settings) {
			$this->_checkWorkers($queue, $force);
		}
	}

	public function start($queue = null) {
		$this->run($queue, true);
	}

	public function stop($queue = null, $worker = null) {
		if (!empty($queue)) {
			if (!isset($this->_queues[$queue])) {
				$this->error("{$queue} does not exist", 'error');
				return;
			}
		} else {
			foreach ($this->_queues as $queue => $settings) {
				$this->stop($queue);
			}
			return;
		}
		$w = $worker ? " worker #{$worker}" : '';
		if ($this->_stopWorker($queue, $worker)) {
			$this->out("{$queue}{$w} stop sent");
		} else {
			$this->error("{$queue}{$w} could not send stop", 'error');
		}
	}

	public function status($queue, $time = null) {}

	public function archive($queue, $time = null) {}

	protected function _checkWorkers($queue, $force = false) {
		$settings = $this->_queues[$queue];
		$director = $settings['director'];
		if ($director::stopSent($queue)) {
			if (($force && !$director::stopRelease($queue)) || !$force) {
				$this->error("{$queue} stopped", 'error');
				return;
			}
		}
		$now = time();
		$ttl = $settings['ttl'] ?: 0;
		$worker = 0;
		while (++$worker <= $settings['workers']) {
			if ($active = $director::lastActivity($queue, $worker)) {
				$this->out("{$queue} worker #{$worker} last active at {$active}");
			}
			if ($active + $ttl < $now || $force) {
				$this->_startWorker($queue, $worker, $force);
			}
		}
	}

	protected function _startWorker($queue, $worker, $force = false) {
		$settings = $this->_queues[$queue];
		$director = $settings['director'];
		if ($director::stopSent($queue, $worker)) {
			if (($force && !$director::stopRelease($queue, $worker)) || !$force) {
				$this->error("{$queue} worker #{$worker} stopped", 'error');
				return;
			}
		}
		$this->out("{$queue} worker #{$worker} dispatching");
		$this->_dispatchWorker($queue, $worker);
	}

	protected function _dispatchWorker($queue, $worker) {
		$args = array('JobWorker', 'run') + compact('queue', 'worker');
		$logPath = Libraries::get(true, 'resources');
		$outfile = "{$logPath}/tmp/logs/{$queue}.worker.{$worker}.log";
		return $this->_execute($args, true, $outfile, true);
	}
	protected function _stopWorker($queue, $worker = null) {
		$settings = $this->_queues[$queue];
		$director = $settings['director'];
		return $director::stopSend($queue, $worker);
	}
}