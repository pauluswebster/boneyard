<?php

use lithium\net\http\Router;
use lithium\core\Environment;


Router::connect('/', 'Jobs::index');
Router::connect('/jobs/status/{:status:\w+}', array('controller' => 'jobs', 'action' => 'index'));
Router::connect('/jobs/status/{:status:\w+}/page:{:page:\d+}', array('controller' => 'jobs', 'action' => 'index'));
Router::connect('/tasks/status/{:status:\w+}', array('controller' => 'tasks', 'action' => 'index'));
Router::connect('/tasks/status/{:status:\w+}/page:{:page:\d+}', array('controller' => 'tasks', 'action' => 'index'));
Router::connect('/reports/{:report:\w+}/{:args}', array('controller' => 'reports', 'action' => 'index'));

Router::connect('/{:action:(start|stop|complete)}/{:job_id:\d+}', array('controller' => 'work_units', 'action' => 'start'));
Router::connect('/{:action:(start|stop|complete)}/{:job_id:\d+}/{:task_id:\d+}', array('controller' => 'work_units', 'action' => 'start'));

if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => 'lithium\test\Controller'));
	Router::connect('/test', array('controller' => 'lithium\test\Controller'));
}

Router::connect('/{:controller}/{:action}/{:id:\d+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:\d+}');
Router::connect('/{:controller}/{:action}/{:args}');
?>