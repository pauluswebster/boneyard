<?php
	use lithium\core\Libraries;
	use lithium\net\http\Router;
	use lithium\action\Dispatcher;
	use lithium\core\Environment;

/**
 * Config
 */
	$config  = Environment::get('centrifuge');
	extract($config);

	Dispatcher::config(array('rules' => array(
		'client' => array('action' => 'client_{:action}')
	)));

	Router::connect($base, array('controller' => 'Views', 'action' => 'display', 'library' => 'centrifuge'), array(
		'persist' => array('library', 'controller')
	));

	Router::connect($base . 'client', array('controller' => 'Views', 'action' => 'display', 'library' => 'centrifuge', 'client' => true), array(
		'persist' => array('library', 'controller', 'client')
	));

	Router::connect($base . 'client/{:args}', array('client' => true, 'library' => 'centrifuge'), array(
		'continue' => true,
		'persist' => array('library', 'controller', 'client')
	));

	Router::connect($base . '{:args}', array('library' => 'centrifuge'), array(
		'continue' => true,
		'persist' => array('library', 'controller')
	));
?>