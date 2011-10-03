<?php

use lithium\net\http\Router;
use lithium\core\Environment;


Router::connect('/', 'Jobs::index');
Router::connect('/jobs/status/{:status:\w+}', array('controller' => 'jobs', 'action' => 'index'));
Router::connect('/reports/{:report:\w+}/{:args}', array('controller' => 'reports', 'action' => 'index'));

if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => 'lithium\test\Controller'));
	Router::connect('/test', array('controller' => 'lithium\test\Controller'));
}

Router::connect('/{:controller}/{:action}/{:id:\d+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:\d+}');
Router::connect('/{:controller}/{:action}/{:args}');
?>