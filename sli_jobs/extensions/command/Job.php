<?php

namespace sli_jobs\extensions\command;

class Job extends \lithium\console\Command {

	protected $_job;

	protected $_pid;

	public function run($command = null) {
		extract($this->request->params);
		if (rand(0,1)) {
			throw new \Exception('boo');
		}
		if (isset($job)) {
			$this->_job = $job;
			$this->_pid = getmypid();
			$this->out("working on job #{$this->_job}");
		}
	}
}