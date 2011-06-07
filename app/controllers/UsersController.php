<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\controllers;

use slicedup_core\action\FlashMessage;
use slicedup_scaffold\core\Scaffold;

class UsersController extends \slicedup_users\controllers\UsersController {
	
	public $scaffold;
	
	public function _init() {
		$this->_render['negotiate'] = true;
		parent::_init();
	}
	
	public function edit(){
		if (!$this->_user->admin && $this->request->id != $this->_user->id) {
			FlashMessage::error('Permision Denied. <sup>[U25]</sup>');
			return $this->redirect('/');
		}
		$this->_render['hasRendered'] = true;
		Scaffold::invoke($this);
	}
}

?>