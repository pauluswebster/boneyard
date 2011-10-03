<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use app\util;

use app\util\Reports;
use sli_util\action\FlashMessage;

class ReportsController extends \lithium\action\Controller {

	public function index() {
		$report = 'completed';
		if (!empty($this->request->report)) {
			$report = $this->request->report;
		} else {
			$this->redirect(compact('report'));
		}
		$args = array('user_id' => $this->_user->id);
		if ($this->request->args) {
			$args = $this->request->args + $args;
		}
		if (!($reportData = Reports::run($report, $args))) {
			FlashMessage::error('Invalid Report.');
			$this->redirect('reports::index');
		}
		$reports = Reports::available();
		$this->set(compact('reportData', 'reports'));
	}

}
?>