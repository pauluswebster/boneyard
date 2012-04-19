<?php

use lithium\action\Dispatcher;
use sli_users\security\User;
use sli_base\action\FlashMessage;

/**
 * Enforce staging user
 */
Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$controller = $params['callable'];
	$user =& User::instance('staging');
	if (!$user->get() && $controller->request->url != 'staging/login') {
		$required = $user->required($controller);
		if ($required instanceOf \lithium\action\Response) {
			FlashMessage::write(array('message' => 'Please login.', 'class' => 'nofade'));
			return $required;
		}
	}
	return $chain->next($self, $params, $chain);
});