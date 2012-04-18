<?php
use lithium\storage\Cache;
use lithium\core\Environment;

if (!Environment::is('production')) {
	if ($log = Cache::read('query', 'log')) {
		echo '<h4>Connection Query Log</h4>';
		echo '<pre>';
		print_r($log);
		echo '</pre>';
	}
	
	if ($error = Cache::read('query', 'error')) {
		echo '<h4>Connection Query Errors</h4>';
		echo '<pre>';
		print_r($error);
		echo '</pre>';
	}	
}
?>