<?php

namespace app\models;

use lithium\storage\Cache;
use sli_base\util\filters\Behaviors;

class Categories extends AppModel {
	
	public static function __init() {
		Behaviors::apply(__CLASS__, 'Tree', array(
			'parent' => 'category_id',
			'left' => 'category_left',
			'right' => 'category_right'
		));
		parent::__init();
	}
	
	public static function selectList($category_id = 0) {
		$key = 'category-select-list-' . $category_id;
		if (!($result = Cache::read('default', $key))) {
			if ($result = static::find('list', array('conditions' => compact('category_id')))) {
				Cache::write('default', $key, $result, '+1 day');
			}
		}
		return $result;
	}
	
	public static function classList($category_id = null) {
		return array('Cars');
	}
}

?>