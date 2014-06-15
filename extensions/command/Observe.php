<?php

namespace sli_jobs\extensions\command;

class Observe extends \lithium\console\Command {

	public function run($command = null) {
		date_default_timezone_set('Pacific/Auckland');
		if ($command == 'observe') {
			$this->_observe();
		} else {
			$this->_queue();
		}
	}

	protected function _queue() {
		while(true) {
			$jobs = $this->_getQueueJobs();
			$this->out(date('[r] ') . count($jobs) . ' jobs to queue.', 'success');
			if ($jobs) {
				array_map(array($this, '_execute'), $jobs);
			}
			sleep(5);
			$this->_m();
		}
	}

	protected function _m() {
		$convert = function ($size, $format = '%s %s') {
			$unit = array('B','KB','MB','GB','TB','PB');
			return sprintf($format, @round($size/pow(1024,($i=floor(log($size,1024)))),2), $unit[$i]);
		};
		$this->out(date('[r] ') . 'Memory:' . $convert(memory_get_usage()) . ' / Peak:' . $convert(memory_get_peak_usage()), 'error');
	}

	protected function _execute($job) {
		$command = 'observe';
		$args = array($job);
		array_unshift($args, $command);
		$arg = implode(' ', $args);
		$file = $this->request->env('PHP_SELF');
		$cmd = "php -f {$file} {$this->request->params['command']} {$arg}";
		$this->_exec($cmd);
		$this->out(date('[r] ') . "queued: `{$cmd}`", 'command');
	}

	protected function _exec($cmd, $outfile = '/dev/null', $errfile = '/dev/null') {
		exec(sprintf("%s > %s 2>&1 &", $cmd, $outfile, $errfile));
	}

	protected function _observe() {
		$log = date('[r] ') . print_r($this->request->params, 1);
		$logFile = '/Users/websta/Sites/Progression/monito/app/app/resources/tmp/logs/out.log';
		file_put_contents($logFile, $log);
	}

	protected function _getQueueJobs() {
		$n = rand(0,20);
		$jobs = array();
		if ($n) {
			while (count($jobs) < $n) {
				$jobs[]  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
			}
		}
		return $jobs;
	}

}

?>