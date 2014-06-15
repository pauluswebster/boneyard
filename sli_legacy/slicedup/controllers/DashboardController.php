<?php
namespace slicedup\controllers;

use \slicedup_users\security\CurrentUser;

class DashboardController extends \lithium\action\Controller {

	public function index(){
		$user = CurrentUser::required('sdu', $this);
		$this->set(compact('user'));
	}

}
?>