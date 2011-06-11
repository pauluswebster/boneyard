<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\controllers;

use app\App;

class SettingsController extends \lithium\action\Controller {
	
	public function announce() {
		$success = false;
		if (!empty($this->request->data)) {
			$message = '';
			if (!empty($this->request->data['announce'])) {
				$message = trim(strip_tags($this->request->data['announce']));
			}
			$success = App::announce($message);
		}
		$this->_render['data'] = compact('success');
		$this->render(array('type' => 'json'));
	}
}
?>