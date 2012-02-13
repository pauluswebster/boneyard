<?php
namespace app\controllers;

use lithium\core\Environment;
use lithium\action\DispatchException;
use sli_util\storage\Registry;
use app\models\Redirects;

class RedirectsController extends ServiceController {
	
	public function hop() {
		$redirect = Redirects::first(array(
			'conditions' => array(
				'segment' => $this->request->args[0]
			)
		));
		if (!$redirect) {
			throw new DispatchException('Not found.');
		}
		$redirect->set(array('hops' => $redirect->hops + 1));
		$redirect->save();
		if (false && !$redirect->track) {
			$status = Environment::is('production') ? 301 : 302;
			return $this->redirect($redirect->url, array('status' => $status, 'exit' => true));
		}
		$this->set(array(
			'analytics' => Registry::get('env.analytics.service'),
			'redirect' => $redirect->data()
		));
		return $this->render(array('layout' => false));
	}
	
}

?>
