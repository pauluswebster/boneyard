<?php

use lithium\action\Dispatcher;
use lithium\core\Environment;
use sli_users\security\User;
use sli_base\action\FlashMessage;

/**
 * Apply dispatch filters
 */
Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	if (Environment::is('staging')) {
		/*
		 * Enforce staging user
		 */
		$controller = $params['callable'];
		$user =& User::instance('staging');
		if (!$user->get() && $controller->request->url != 'staging/login') {
			$required = $user->required($controller);
			if ($required instanceOf \lithium\action\Response) {
				FlashMessage::write(array('message' => 'Please login.', 'class' => 'nofade'));
				return $required;
			}
		}
	}
	return $chain->next($self, $params, $chain);
});