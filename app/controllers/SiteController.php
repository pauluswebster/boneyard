<?php

namespace app\controllers;

class SiteController extends AppController {
	
	public function _init() {
		parent::_init();
		$this->_render['layout'] = 'site';
	}	
}

?>