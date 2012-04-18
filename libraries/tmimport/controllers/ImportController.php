<?php

namespace tmimport\controllers;

use tmimport\models\TMListings;
use tmimport\models\TMCategories;

class ImportController extends \lithium\action\Controller {
		
	public function index() {
//		TMListings::importRecords();
		if (!empty($this->request->args)) {
			$query = reset($this->request->args);
			TMListings::importData($query);
		}	
		die;
	}
}

?>