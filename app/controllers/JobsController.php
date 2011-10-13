<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use app\models\Jobs;
use app\models\JobLogs;
use sli_util\storage\Registry;
use sli_util\action\FlashMessage;

class JobsController extends \lithium\action\Controller {

	public $scaffold;

	protected function _scaffold($controller, $params, $options) {
		$controller->applyFilter('index', function($self, $params, $chain){
			$conditions = array(
				'user_id' => $self->_user->id(),
			);
			$status = 'current';
			if (!empty($self->request->status)) {
				$status = $self->request->status;
			}
			$conditions += compact('status');
			$recordSet = Jobs::all(array(
				'conditions' => $conditions,
				'order' => 'due asc, completed desc'
			));
			$statuses = array_reverse(array_keys(Jobs::statuses()));

			$tz = new \DateTimeZone($self->_user->timezone);
			$date = new \DateTime(null, $tz);
			$format = Registry::get('app.date.long');
			$active = false;
			if ($job = $self->_user->job(true)) {
				$active = $job->job->id;
			}
			$params = compact('statuses', 'status', 'recordSet', 'date', 'format', 'active') + $params;
			return $chain->next($self, $params, $chain);
		});

		$filter = function($self, $params, $chain){
			$params['actions'] = array();
			return $chain->next($self, $params, $chain);
		};

		$controller->applyFilter('edit', $filter);
		$controller->applyFilter('add', $filter);

	}

	public function start() {
		if ($this->request->id) {
			$started = JobLogs::start($this->_user->id, $this->request->id);
			if ($started) {
				$this->_user->store('job.current', $started);
				FlashMessage::success("Started work on #{$this->request->id}.");
			} else {
				FlashMessage::error("Invalid job #{$this->request->id}.");
			}
		} else {
			FlashMessage::error("Invalid job.");
		}
		$this->redirect('jobs::index');
	}

	public function stop() {
		if($job = JobLogs::stop($this->_user->id)) {
			$this->_user->eliminate('job.current');
			FlashMessage::success("Stopped work on #{$job->id}.");
		} else {
			FlashMessage::success("Stopped work.");
		}
		$this->redirect('jobs::index');
	}

	public function complete() {
		if ($this->request->id && $job = Jobs::first($this->request->id)) {
			if ($job->completed) {
				FlashMessage::error("Job #{$job->id} already completed.");
			} else {
				$job->completed = time();
				$job->save();
				FlashMessage::success("Job #{$job->id} completed.");
			}
		} else {
			FlashMessage::error("Invalid job.");
		}
		$this->redirect('jobs::index');
	}
}

?>