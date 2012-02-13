<?php
namespace app\controllers;

class PagesController extends SiteController {

	public function view() {
		$args = func_get_args();
		$path = empty($args) || empty($args[0]) ? array('home') : $args;
		return $this->render(array('template' => join('/', $path)));
	}
	
	public function contact () {
		
	}
}

?>