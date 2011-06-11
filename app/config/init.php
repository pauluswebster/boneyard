<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\action\Dispatcher;
use lithium\action\Response;
use lithium\core\Libraries;
use lithium\core\Environment;
use lithium\util\Inflector;
use slicedup_core\configuration\Registry;
use slicedup_core\configuration\LibraryRegistry;
use slicedup_core\action\FlashMessage;
use slicedup_core\net\http\Media;
use slicedup_scaffold\core\Scaffold;

//Development
Environment::is(function(){
	return 'development';
});

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

//Add media type for ajax calls
$content = array(
	'text/html', 'application/xhtml+xml',//html
	'application/javascript', 'text/javascript'//js
);
$options = array(
	'paths' => array(
		'template' => array(
			'{:library}/views/{:controller}/{:template}.ajax.php',
			'{:library}/views/{:controller}/{:template}.html.php',
		),
		'layout' => array(
			'{:library}/views/layouts/{:layout}.ajax.php',
			'{:library}/views/layouts/{:layout}.html.php',
		),
		'element' => array(
			'{:library}/views/elements/{:template}.ajax.php',
			'{:library}/views/elements/{:template}.html.php'
		)
	),
	'conditions' => array('ajax' => true)
);
Media::type('ajax', $content, $options + Media::defaultOptions());

//Apply ajax templates to scaffold paths
Scaffold::applyFilter('paths', function($self, $params, $chain){
	extract($params);
	$scaffold = Libraries::get('slicedup_scaffold');
	Media::addPaths('ajax', array(
		'template' => array(
			'{:library}/views/scaffold/{:template}.{:type}.php',
			'{:library}/views/scaffold/{:template}.html.php',
			$scaffold['path'] . '/views/scaffold/{:template}.html.php'
		),
		'layout' => array(
			LITHIUM_APP_PATH . '/views/layouts/{:layout}.{:type}.php',
			LITHIUM_APP_PATH . '/views/layouts/{:layout}.html.php'
		)
	), false);
	
	if ($name) {
		list($library, $name) = explode('\\', $name);
		$library = Libraries::get($library);
		Media::addPaths('ajax', array(
			'template' => array(
				$library['path'] . '/views/'.$name.'/{:template}.{:type}.php',
				$library['path'] . '/views/'.$name.'/{:template}.html.php',
				$library['path'] . '/views/{:controller}/{:template}.{:type}.php',
				$library['path'] . '/views/{:controller}/{:template}.html.php'
			),
			'layout' => array(
				$library['path'] . '/views/layouts/{:layout}.{:type}.php',
				$library['path'] . '/views/layouts/{:layout}.html.php'
			)
		));
	}

	return $chain->next($self, $params, $chain);
});

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

//Dispatch/Controller filters

Dispatcher::applyFilter('_callable', function($self, $params, $chain) use ($config) {
	$controller = $chain->next($self, $params, $chain);
	if(get_class($controller) == 'lithium\test\Controller') {
		return $controller;
	}
	
	//ajax delay
	$controller->applyFilter('__invoke', function($self, $params, $chain) {
        if($self->request->is('ajax')) {
//        	sleep(1);
        }
        return $chain->next($self, $params, $chain);
	});
	
	//ajax redirect filter
	$controller->applyFilter('redirect', function($self, $params, $chain) {
        $router = '\lithium\net\http\Router';
        if($self->request->is('ajax')) {
        	$options = $params['options'];
        	$location = $options['location'] ?: $router::match($params['url'], $self->request);
        	$self->response = new Response(array('body' => "<script>window.location = '{$location}';</script>"));
        	$self->response->render();
			$self->invokeMethod('_stop');
        }
        return $chain->next($self, $params, $chain);
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
	if(!isset($controller->_user)) {
		return $chain->next($self, $params, $chain);
	}
	
	//access checking
	$user = $controller->_user;
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
				FlashMessage::write(array('message' => 'Please login.', 'class' => 'nofade'));
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