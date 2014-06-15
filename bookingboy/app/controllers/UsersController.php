<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
namespace app\controllers;

use sli_util\action\FlashMessage;
use sli_scaffold\core\Scaffold;

class UsersController extends \sli_users\controllers\UsersController {
	
	public $scaffold;
	
	public function _init() {
		$this->_render['negotiate'] = true;
		parent::_init();
	}
	
	public function edit(){
		if (empty($this->request->id)) {
			$this->request->id = $this->_user->id;
		}
		if (!$this->_user->admin && $this->request->id != $this->_user->id) {
			FlashMessage::error('Permision Denied.');
			return $this->redirect('/');
		}
		$this->_render['hasRendered'] = true;
		$params = array();
		$scaffold = Scaffold::callable($this, $params);

		$scaffold->applyFilter('edit', function($self, $params, $chain){
			if($self->request->is('ajax')) {
				if (!$self->_user->admin || $self->request->id == $self->_user->id) {
					$params['redirect'] = '/';
				}
				$params['singular'] = 'Account';
			}
			return $chain->next($self, $params, $chain);
		});

		$this->response = Scaffold::call($scaffold, $params);
	}
}

?>