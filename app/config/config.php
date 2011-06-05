<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\action\Dispatcher;
use lithium\util\Inflector;
use slicedup_core\configuration\Registry;
use slicedup_core\configuration\LibraryRegistry;
use slicedup_scaffold\core\Scaffold;

//Load the scaffold media paths to enale ajax templates
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
	'start' => 'PT8H',
	'end' => 'PT21H'
);
Registry::set('bookings', $config);

$actionMap = array(
	'public' => array(
		'users::login', 
		'users::logout', 
		'users::password_reset'
	),
	'user' => array(
		'bookings::index',
		'users::edit'
	)
);

Dispatcher::applyFilter('_callable', function($self, $params, $chain) use ($config, $actionMap) {
	$controller = $chain->next($self, $params, $chain);
	
	//ajax redirect filter
	$controller->applyFilter('redirect', function($self, $params, $chain) {
        $router = '\lithium\net\http\Router';
        if($self->request->is('ajax')) {
        	$options = $params['options'];
        	$location = $options['location'] ?: $router::match($params['url'], $self->request);
        	echo "<script>window.location = '{$location}';</script>";
        	$self->invokeMethod('_stop');
        }
        return $chain->next($self, $params, $chain);
	});
	
	//set config & auth user
	$controller->_settings = $config;
	$currentUser = '\slicedup_users\security\CurrentUser';
	$user = $controller->_user = $currentUser::instance('users');
	$controller->set(array(
		'user' => $controller->_user,
		'settings' => $controller->_settings
	));
	
	//access checking
	$r = $controller->request->params;
	$r['controller'] = preg_replace('/(.*\\\)?(.*)(Controller)/', '$2', $r['controller']);
	$action = Inflector::underscore($r['controller']) . "::" . $r['action'];
	if (!$user->get()) {
		if(!in_array($action, $actionMap['public'])) {
			return function() use ($controller) {
				return $controller->_user->required($controller);
			};
		}
	} elseif (!$user->admin) {	
		if(!in_array($action, $actionMap['user']) && !in_array($action, $actionMap['public'])) {
			return function() use($controller){
				return $controller->redirect('/', array('exit' => true));	
			};
		}
	}	
	return $controller;
});
?>