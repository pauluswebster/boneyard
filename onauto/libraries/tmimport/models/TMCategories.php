<?php

namespace tmimport\models;

use app\models\Categories;

class TMCategories extends \lithium\data\Model {
	
	protected $_meta = array(
		'connection' => false
	);
	
	public static function importData() {
		return;
		$path = LITHIUM_APP_PATH . '/resources/Categories';
		if (!is_dir($path)) {
			@mkdir($path, 0777, true);
		}
		$data = file_get_contents('http://api.trademe.co.nz/v1/Categories/UsedCars.json');
		file_put_contents($path . '.json', $data);
	}
	
	public function importRecords() {
		return;
		$path = LITHIUM_APP_PATH . '/resources/Categories';
		$data = json_decode(file_get_contents($path . '.json'), true);
		foreach ($data['Subcategories'] as $subcat) {
			$cat = Categories::create(array(
				'category_id' => 1,
				'title' => $subcat['Name']
			));
			$cat->save();
		}
	}
}