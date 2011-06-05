<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\controllers;

class UsersController extends \slicedup_users\controllers\UsersController {
	
	public $scaffold;
	
	public function _init() {
		$this->_render['negotiate'] = true;
		parent::_init();
	}
	
	public function update(){}
}

?>