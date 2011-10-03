<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\action\Dispatcher;
use lithium\core\Environment;
use lithium\util\Inflector;
use app\security\User;
use sli_util\action\FlashMessage;
use sli_util\storage\Registry;
use sli_scaffold\core\Scaffold;

/**
 * Environment
 */
Environment::is(function($request) {
//	return 'production';
	$isLocal = in_array($request->env('HTTP_HOST'), array('jobs.dev'));
	$isCli = is_array($request->argv) && !empty($request->argv);
	switch (true) {
		case (isset($request->params['env'])):
			return $request->params['env'];
		case ($isCli && $request->argv[0] == 'test'):
			return 'test';
		case (preg_match('/^test/', $request->env('HTTP_HOST'))):
			return 'test';
		case (preg_match('/^test\/*$/', $request->url) && $isLocal):
			return 'test';
		case ($isCli || $isLocal):
			return 'development';
		default:
			return 'production';
	}
});

/**
 * Load App Config
 */
Registry::load(__DIR__ . '/app.config.php', array(), 'app');

/**
 * User class init
 */
User::instance('default');

/**
 * Scaffold config
 */
Scaffold::config(Registry::get('app.scaffold'));

/**
 * Controller setup
 */
Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
	$controller = $chain->next($self, $params, $chain);
	if(get_class($controller) == 'lithium\test\Controller') {
		return $controller;
	}
	$controller->_user = User::instance('default');
	$controller->set(array(
		'locale' => Environment::get('locale'),
		'user' => $controller->_user
	));
	return $controller;
});


/**
 * Controller Auth
 */
Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$controller = $params['callable'];
	if(!isset($controller->_user)) {
		return $chain->next($self, $params, $chain);
	}

	$r = $controller->request->params;
	if (isset($controller->scaffold)) {
		$r['controller'] = $controller->scaffold['controller'];
	}
	$r['controller'] = preg_replace('/(.*\\\)?(.*)(Controller)/', '$2', $r['controller']);
	$action = Inflector::underscore($r['controller']) . "::" . $r['action'];

	$actionMap = Registry::get('app.actions');

	$user =& $controller->_user;
	if (!$user->get()) {
		if(!in_array($action, $actionMap['public'])) {
			$required = $user->required($controller);
			if ($required instanceOf \lithium\action\Response) {
				FlashMessage::write(array('message' => 'Please login.', 'class' => 'nofade'));
				return $required;
			}
		}
	} elseif (!$user->admin && in_array($action, $actionMap['admin'])) {
		FlashMessage::error('Permision Denied.');
		return $controller->redirect('/', array('exit' => true));
	}

	return $chain->next($self, $params, $chain);
});
?>