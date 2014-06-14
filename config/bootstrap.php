<?php
	use lithium\core\Libraries;
	use lithium\data\Connections;
	use sli_scaffold\core\Scaffold;
	use lithium\util\collection\Filters;
	use lithium\core\Environment;
	use lithium\util\Inflector;
	use lithium\storage\Cache;

/**
 * Required Libraries
 */
	Libraries::add('sli_base');
	Libraries::add('sli_scaffold');
	Libraries::add('sli_bootstrap');


/**
 * Misc
 */
	Inflector::rules('uninflected', 'staff');

/**
 * Connection setting/creation
 */
	$defaults = array(
		'connection' => 'default',
		'base' => '/'
	);
	$config = Libraries::get('centrifuge', 'config') ?: array();
	$config += $defaults;
	extract($config);
	$base = rtrim($base, '/') . '/';

	if (is_array($connection)) {
		$source = null;
		$conn = $connection;
		$connection = 'centrifuge';
	} else {
		$source = Connections::get($connection, array('config' => true));
		$conn = array();
	}
	if (!$source) {
		$source = $conn + array(
		 	'type' => 'database',
		 	'adapter' => 'MySql',
		 	'host' => 'localhost',
		 	'login' => 'centrifuge',
		 	'password' => 'centrifuge',
		 	'database' => 'centrifuge',
		 	'encoding' => 'UTF-8'
		);
		Connections::add($connection, $source);
	}

	if ($source['type'] == 'database') {
		/**
		 * Connection cache in production
		 */
		Connections::get($connection)->applyFilter("describe", function($self, $params, $chain) use($connection) {
//			if (!Environment::is('production')) {
//				return $chain->next($self, $params, $chain);
//			}
			static $sources = array();
			$key = "$connection.sources";
			if (empty($sources)) {
				$sources = Cache::read('default', $key) ?: array();
			}
			$name = $params['entity'];
			if (!isset($sources[$name])) {
				$sources[$name] = $chain->next($self, $params, $chain);
				Cache::write('default', $key, $sources);
			}
			return $sources[$name];
		});
		/**
		 * Connection debug when not in production
		 */
		Connections::get($connection)->applyFilter("_execute", function($self, $params, $chain) {
			if (Environment::is('production')) {
				return $chain->next($self, $params, $chain);
			}
			$log = Cache::read('default', 'query-log') ?: array();
			$log[] = $params['sql'];
			Cache::write('default', 'query-log', $log);
		    try {
		    	 return $chain->next($self, $params, $chain);
		    } catch (PDOException $e) {
		    	$error = Cache::read('default', 'query-error') ?: array();
		    	$error[] = $e->getMessage();
		    	Cache::write('default', 'query-error', $error);
		    }
		   	return false;
		});
	}


	Environment::is(function(){
		$configured = false;//load from somewhere!
		return $configured ? $configured : 'development';
	});

	Environment::set('centrifuge', compact(array_keys($config)));

/**
 * Pass connection through to base model
 */
	Filters::apply('centrifuge\models\Centrifuge', '__setup', function($self, $params, $chain) use ($connection){
		$params['connection'] = $connection;
		return $chain->next($self, $params, $chain);
	});

/**
 * Scaffold config
 */
	Scaffold::config(array(
		'all' => false,
		'centrifuge' => array(
			'prefixes' => array(
				'default' => '',
				'client' =>  'client_',
			),
			'paths' => 'sli_bootstrap',
			'connection' => $connection
		),
		'scaffold' => array(
			'centrifuge.views',
			'centrifuge.clients' => array(
				'prefixes' => array('default')
			),
			'centrifuge.contacts' => array(
			),
			'centrifuge.staff' => array(
				'prefixes' => array('default')
			),
			'centrifuge.departments' => array(
				'prefixes' => array('default')
			),
			'centrifuge.projects' => array(
				'actions' => array(
					'index',
					'view',
					'add' => array('default'),
					'edit' => array('default'),
					'delete' => array('default')
				)
			),
			'centrifuge.milestones',
			'centrifuge.tasks',
			'centrifuge.logs',
			'centrifuge.comments',
		)
	));

?>