<?php
use lithium\storage\Cache;
use lithium\core\Environment;

if (!Environment::is('production')) {
	if ($log = Cache::read('default', 'query-log')) {
		echo '<h4>Connection Query Log</h4>';
		echo '<pre>';
		print_r($log);
		echo '</pre>';
		Cache::delete('default', 'query-log');
	}
	
	if ($error = Cache::read('default', 'query-error')) {
		echo '<h4>Connection Query Errors</h4>';
		echo '<pre>';
		print_r($error);
		echo '</pre>';
		Cache::delete('default', 'query-error');
	}	
}
?>