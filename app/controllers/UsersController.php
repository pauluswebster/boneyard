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
			FlashMessage::error('Permision Denied.');
			return $this->redirect('/');
		}
		$this->_render['hasRendered'] = true;
		$params = array();
		$scaffold = Scaffold::callable($this, $params);
		if (!$this->_user->admin || $this->request->id == $this->_user->id) {
			$scaffold->applyFilter('redirect', function($self, $params, $chain) {
				if($self->request->is('ajax')) {
					$params['options']['location'] = '/';
					return $params;
				}
				return $chain->next($self, $params, $chain);
			});
		}
		$this->response = Scaffold::call($scaffold, $params);
	}
}

?>