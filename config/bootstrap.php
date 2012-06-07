<?php
use lithium\core\Libraries;
use lithium\data\Connections;
use sli_scaffold\core\Scaffold;

Libraries::add('sli_base');
Libraries::add('sli_scaffold');
Libraries::add('sli_bootstrap');

Connections::add('centrifuge', array(
 	'type' => 'database',
 	'adapter' => 'MySql',
 	'host' => 'localhost',
 	'login' => 'centrifuge',
 	'password' => 'centrifuge',
 	'database' => 'centrifuge',
 	'encoding' => 'UTF-8'
));

Scaffold::config(array(
	'all' => false,
	'prefixes' => array(
		'default' => '',
		'client' =>  'client_',
	),
	'centrifuge' => array(
		'connection' => 'centrifuge'
	),
	'scaffold' => array(
		'centrifuge.clients' => array(
			'paths' => 'sli_bootstrap',
			'prefixes' => array('default')
		),
		'centrifuge.projects' => array(
			'paths' => 'sli_bootstrap',
			'actions' => array(
				'index',
				'view',
				'add' => array('default'),
				'edit' => array('default'),
				'delete' => array('default')
			)
		)
	)
));