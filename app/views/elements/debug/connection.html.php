<?php
use lithium\storage\Cache;

if ($log = Cache::read('query', 'log')) {
	echo '<h4>Connection Query Log</h4>';
	var_dump($log);
}

if ($error = Cache::read('query', 'error')) {
	echo '<h4>Connection Query Errors</h4>';
	var_dump($error);
}