<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use app\models\Jobs;
use app\models\Tasks;
use app\models\JobLogs;
use sli_util\storage\Registry;
use sli_util\action\FlashMessage;
use lithium\action\DispatchException;

class WorkUnitsController extends \lithium\action\Controller {

	public $scaffold;

	protected function _init() {
		parent::_init();
		$this->applyFilter('__invoke', function($self, $params, $chain){
			if (get_class($self) == __CLASS__) {
				if (!in_array($self->request->action, get_class_methods($self))) {
					$message = '%s::%s cannot be requested directly.';
					$args = array(__CLASS__, $self->request->action);
					throw new DispatchException(vsprintf($message, $args));
				}
			}
			return $chain->next($self, $params, $chain);
		});
	}

	protected function _scaffold($controller, $params, $options) {
		$controller->applyFilter('index', function($self, $params, $chain){
			$model = $params['model'];
			$conditions = array();
			if (isset($params['conditions'])) {
				$conditions = $params['conditions'];
			}
			$conditions['user_id'] = $self->_user->id();
			$status = 'current';
			if (!empty($self->request->status)) {
				$status = $self->request->status;
			}
			$conditions += compact('status');
			$recordSet = $model::all(array(
				'conditions' => $conditions,
				'order' => 'due asc, completed desc'
			));
			$statuses = array_reverse(array_keys($model::statuses()));

			$tz = new \DateTimeZone($self->_user->timezone);
			$date = new \DateTime(null, $tz);
			$format = Registry::get('app.date.long');
			$active = $self->_user->active();
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
		$redirect = 'jobs::index';
		if ($this->request->job_id) {
			JobLogs::stop($this->_user->id);
			$job = JobLogs::start($this->_user->id, $this->request->job_id, $this->request->task_id);
			if ($job) {
				$this->_user->active($job);
				FlashMessage::success("Started work on job #{$job->job_id}.");
			} else {
				FlashMessage::error("Invalid job #{$this->request->job_id}.");
			}
		} else {
			FlashMessage::error("Invalid job.");
		}
		if (isset($job, $job->task_id)) {
			$redirect = array(
				'controller' => 'tasks',
				'action' => 'active_job',
				'id' => $job->job_id
			);
		}
		$this->redirect($redirect);
	}

	public function stop() {
		$redirect = 'jobs::index';
		if($job = JobLogs::stop($this->_user->id)) {
			$this->_user->eliminate('job.current');
			FlashMessage::success("Stopped work on job #{$job->job_id}.");
		} else {
			FlashMessage::success("Stopped work.");
		}
		if (isset($job, $job->task_id)) {
			$redirect = array(
				'controller' => 'tasks',
				'action' => 'active_job',
				'id' => $job->job_id
			);
		}
		$this->redirect($redirect);
	}

	public function complete() {
		$redirect = 'jobs::index';
		if ($this->request->job_id && $job = Jobs::first($this->request->job_id)) {
			if ($this->request->task_id) {
				if ($task = Tasks::first($this->request->task_id)) {
					if ($task->completed) {
						FlashMessage::error("Task #{$task->id} already completed.");
					} else {
						$task->completed = time();
						$task->save();
						FlashMessage::success("Task #{$task->id} completed.");
					}
				} else {
					FlashMessage::error("Invalid task #{$this->request->task_id}.");
				}
			} else {
				if ($job->completed) {
					FlashMessage::error("Job #{$job->id} already completed.");
				} else {
					$job->completed = time();
					$job->save();
					FlashMessage::success("Job #{$job->id} completed.");
				}
			}
		} else {
			FlashMessage::error("Invalid job #{$this->request->job_id}.");
		}
		if ($this->request->task_id && isset($task)) {
			$redirect = array(
				'controller' => 'tasks',
				'action' => 'active_job',
				'id' => $task->job_id
			);
		}
		$this->redirect($redirect);
	}
}

?>