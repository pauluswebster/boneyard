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
	
	protected function _scaffold($controller, $params, $options) {
		$controller->applyFilter('edit', function($self, $params, $chain){
			$request =& $self->request->params;
			if (!$self->_user->admin || empty($params['id'])) {
				$request['id'] = $self->_user->id;
			}
			return $chain->next($self, $params, $chain);
		});
	}

}

?>