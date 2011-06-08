<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\action\Dispatcher;
use lithium\action\Response;
use lithium\util\Inflector;
use lithium\core\Environment;
use slicedup_core\configuration\Registry;
use slicedup_core\configuration\LibraryRegistry;
use slicedup_core\action\FlashMessage;
use slicedup_scaffold\core\Scaffold;

Environment::is(function(){
	return 'development';
});

//Load the scaffold media paths to enable ajax templates on all
Scaffold::setMediaPaths();

//Configure auth via slicedup_users
LibraryRegistry::add('slicedup_users', 'users', array(
	'model' => array(
		'class' =>	'\app\models\Users'
	),
	'controller' => array(
		'library' => 'app',
		'class' => 'app\controllers\UsersController',
		'actions' => array(
			'login' => 'login',
			'logout' => 'logout',
			'register' => false,
			'password_reset' => 'password_reset'
		)
	),
	'routing' => array(
		'base' => '',
		'loginRedirect' => '/',
		'logoutRedirect' => '/login'
	)
));

//Set Site general config
$config = array(
	'siteName' => 'Tennis Club',
	'timezone' => 'Pacific/Auckland',
	'listingInterval' => 'PT1H',
	'listingIntervalSeconds' => 3600,
	'listingIntervalLabel' => 1,
	'listingIntervalFormat' => 'H:i',
    'bookingInterval' => 'PT1H',
    'datePickerFormat' => 'm/d/Y H:i',
	'start' => 'PT7H',
	'end' => 'PT24H',
	'permissions' => array(
		'attendingCanEdit' => 1
	)
);

Registry::set('bookings', $config);

Dispatcher::applyFilter('_callable', function($self, $params, $chain) use ($config) {
	$controller = $chain->next($self, $params, $chain);
	if(get_class($controller) == 'lithium\test\Controller') {
		return $controller;
	}
	//ajax redirect filter
	$controller->applyFilter('redirect', function($self, $params, $chain) {
        $router = '\lithium\net\http\Router';
        $redirect = $chain->next($self, $params, $chain);
        if($self->request->is('ajax')) {
        	if (is_array($redirect)) {
        		$params = $redirect;
        	}
        	$options = $params['options'];
        	$location = $options['location'] ?: $router::match($params['url'], $self->request);
        	$self->response = new Response(array('body' => "<script>window.location = '{$location}';</script>"));
        	$self->response->render();
			$self->invokeMethod('_stop');
        }
        return $redirect;
	});
	
	//set config & auth user
	$controller->_settings = $config;
	$currentUser = 'slicedup_users\security\CurrentUser';
	$user = $controller->_user = $currentUser::instance('users');
	$controller->set(array(
		'user' => $controller->_user,
		'settings' => $controller->_settings,
		'permissions' => $controller->_settings['permissions']
	));
	
	return $controller;
});

//Auth map
$actionMap = array(
	'public' => array(
		'users::login',
		'users::logout',
		'users::password_reset'
	),
	'user' => array(
		'bookings::index',
		'bookings::add',
		'bookings::edit',
		'users::edit'
	)
//	'admin' => '*' *implied*
);

Dispatcher::applyFilter('_call', function($self, $params, $chain) use ($actionMap) {
	$controller = $params['callable'];
	$user = $controller->_user;
	//access checking
	$r = $controller->request->params;
	if (isset($controller->scaffold)) {
		$r['controller'] = $controller->scaffold['controller'];
	}
	$r['controller'] = preg_replace('/(.*\\\)?(.*)(Controller)/', '$2', $r['controller']);
	$action = Inflector::underscore($r['controller']) . "::" . $r['action'];

	if (!$user->get()) {
		if(!in_array($action, $actionMap['public'])) {
			$required = $controller->_user->required($controller);
			if ($required instanceOf \lithium\action\Response) {
				FlashMessage::write('Please login.');
				return $required;
			}
		}
	} elseif (!$user->admin) {	
		$allowed = (in_array($action, $actionMap['user']) || in_array($action, $actionMap['public']));
		if ($action == 'users::edit' && $r['id'] != $user->id()) {
			$allowed = false;
		}
		if (!$allowed) {
			FlashMessage::error('Permision Denied.');
			return $controller->redirect('/', array('exit' => true));
		}
	}

	return $chain->next($self, $params, $chain);
});
?>