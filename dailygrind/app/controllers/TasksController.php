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
use sli_base\util\Store;

class TasksController extends WorkUnitsController {

	public $scaffold;

	protected function _scaffold($controller, $params, $options) {
		$jobs = Jobs::find('list', array(
			'conditions' => array(
				'status' => 'current',
				'user_id' => $this->_user->id()
			)
		));

		if (!$jobs) {
			$activeJob = false;
		} else {
			$activeJob = $this->_user->retrieve('tasks.active_job');
			if (!$activeJob || !array_key_exists($activeJob, $jobs)) {
				$activeJob = key($jobs);
				$this->_user->store('tasks.active_job', $activeJob);
			}
		}

		if ($activeJob) {
			$controller->applyFilter('index', function($self, $params, $chain) use ($jobs, $activeJob) {
				$params += compact('jobs', 'activeJob');
				if ($activeJob) {
					$params['conditions'] = array(
						'job_id' => $activeJob
					);
				}
				return $chain->next($self, $params, $chain);
			});
		}

		$filter = function($self, $params, $chain) use ($jobs, $activeJob){
			$params += compact('jobs', 'activeJob');
			$call = $chain->next($self, $params, $chain);
			if (is_array($call)) {
				$call = Store::set($call, array(
					'fields.Task.fields.job_id.list'=> $jobs,
					'fields.Task.fields.job_id.default' => $activeJob
				));
			} else if ($call instanceOf \lithium\action\Response && !empty($self->request->data)) {
				$redirect = array('action' => 'index');
				if ($self->request->data['job_id'] != $activeJob) {
					$redirect = array(
						'action' => 'active_job',
						'id' => $self->request->data['job_id']
					);
				}
				return $self->redirect($redirect);
			}

			return $call;
		};

		$controller->applyFilter('edit', $filter);
		$controller->applyFilter('add', $filter);
		parent::_scaffold($controller, $params, $options);
	}

	public function active_job() {
		$this->_user->store('tasks.active_job', $this->request->id);
		$this->redirect(array('action' => 'index'));
	}
}

?>