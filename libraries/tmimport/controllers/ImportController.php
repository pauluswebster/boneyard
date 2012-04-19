<?php

namespace tmimport\controllers;

use tmimport\models\TMListings;
use tmimport\models\TMCategories;

class ImportController extends \lithium\action\Controller {
		
	public function query() {
		if (!empty($this->request->args)) {
			$query = reset($this->request->args);
			echo "Importing {$query}";
			TMListings::importData($query);
		}
		die;
	}
	
	public function setup() {
		$path = LITHIUM_APP_PATH . '/resources/Used';
		@mkdir($path, 0777, true);
		echo "path exists: " . is_dir($path);
		echo "path is writable: " . is_writable($path);
		die;
	}
	
	public function import() {
		TMListings::importRecords();
		die;
	}
}

?>