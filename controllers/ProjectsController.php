<?php

namespace centrifuge\controllers;

class ProjectsController extends \lithium\action\Controller {
	
	public $scaffold;
	
	public function _scaffold($controller, $params, $options) {
		$controller->applyFilter('index', function($self, $params, $chain){
			$params['query']['with'] = array('Staff');
			return $chain->next($self, $params, $chain);
		});
		
		
	}
}

?>