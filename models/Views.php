<?php

namespace centrifuge\models;

use lithium\core\Libraries;

class Views extends Centrifuge {
	
	public $validates = array(
		'title' => 'please enter a title'
	);
	
	public static function load(\lithium\data\Entity $view) {
		
		/**
		 * Likely views are:

		 * 1. Tasks - straight list - filtered on flexible conditions, and default sets (e.g. mine)
		 *  ~ Task
		 *  ~ Task
		 *  ~ Task
		 * 
		 * 2. Tasks - grouped (projects/milestones) - again filtered
		 * 
		 *  ~ Project/Milestone
		 *  	~ Task
		 *  	~ Task
		 *  ~ Project/Milestone
		 *  	~ Task
		 *  	~ Task
		 * 
		 * 3. Projects - with milestones 
		 * 
		 *  ~ Project
		 *  	~ Milestone
		 *  	~ Milestone
		 *  	~ Milestone
		 */
		
		
		
		
		if (!is_array(($settings = $view->settings ?: array()))) {
			$settings = @json_decode($settings, true);
		}
		
		$settings += array(
			'list' => 'Tasks',
			'group' => null
		);
		
		$model = Libraries::locate('models', $settings['List'], array('library' => 'centrifuge'));
		if ($records = $model::all()) {
			
		}
	}
}

?>