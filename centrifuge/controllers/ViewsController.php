<?php

namespace centrifuge\controllers;

use centrifuge\models\Views;

class ViewsController extends \lithium\action\Controller {
	
	public $scaffold;
	
	public function display(){
		if ($record = Views::first(/*conditions*/)) {
			$data = Views::load($record);
			$this->set(compact('view', 'data'));
		} else {
			$this->redirect(array('action' => 'display'));
		}
	}
	
	public function client_display() {}
}

?>