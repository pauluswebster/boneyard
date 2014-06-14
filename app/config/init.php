<?php

use lithium\action\Dispatcher;
use lithium\core\Environment;
use sli_users\security\User;
use sli_base\action\FlashMessage;
use sli_base\net\http\Media;

/**
 * Apply dispatch filters
 */
Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	if (Environment::is('staging') && $params['callable'] instanceof \lithium\action\Controller) {
		/*
		 * Enforce staging user
		 */
		$controller = $params['callable'];
		$user =& User::instance('staging');
		if (!$user->get() && $controller->request->url != '/staging/login') {
			$required = $user->required($controller);
			if ($required instanceOf \lithium\action\Response) {
				FlashMessage::write(array('message' => 'Please login.', 'class' => 'nofade'));
				return $required;
			}
		} elseif ($controller->request->url == '/staging/login') {
			$controller->applyFilter('__invoke', function($self, $params, $chain) {
				Media::addPaths('html', array(
					'layout' => array(
						"{:library}/views/layouts/blank.{:type}.php",
					)
				));
				return $chain->next($self, $params, $chain);
			});
		}
	}
	return $chain->next($self, $params, $chain);
});