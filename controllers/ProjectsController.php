<?php

namespace centrifuge\controllers;

use sli_scaffold\controllers\ScaffoldController;

class ProjectsController extends \lithium\action\Controller {
	
	public $scaffold;
	
//	protected function _scaffold($controller, $params, $options) {
//		$controller->applyFilter('index', function($self, $params, $chain){
//			if ($params['scaffold']['prefix'] == 'client') {
//				$params['query']['conditions'][] = array(
//					'Projects.client_id' => 2
//				);
//			}
//			return $chain->next($self, $params, $chain);
//		});
//	}
//
//	public function client_index() {
//		$this->_render['template'] = 'index';
//		$this->applyFilter('index', function($self, $params, $chain){
//			$params['plural'].= ' Cows';
//			return $chain->next($self, $params, $chain);
//		});
//		return ScaffoldController::scaffoldAction($this, 'index');
//	}
}

?>