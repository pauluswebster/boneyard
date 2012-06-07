<?php
use lithium\net\http\Router;
use lithium\action\Dispatcher;

Dispatcher::config(array('rules' => array(
	'client' => array('action' => 'client_{:action}')
)));

Router::connect('/client/{:args}', array('client' => true), array(
	'continue' => true, 
	'persist' => array('controller', 'client')
));

Router::connect('/projects/{:action}', array('controller' => 'projects', 'library' => 'centrifuge'));
Router::connect('/projects/{:action}/{:id:\d+}', array('controller' => 'projects', 'library' => 'centrifuge'));
Router::connect('/clients/{:action}', array('controller' => 'clients', 'library' => 'centrifuge'));
Router::connect('/clients/{:action}/{:id:\d+}', array('controller' => 'clients', 'library' => 'centrifuge'));

?>