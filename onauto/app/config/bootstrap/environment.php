<?php
use lithium\core\Environment;
use lithium\core\ConfigException;
use sli_base\storage\Registry;

/**
 * Load enviroment config & credentials into registry
 */
$enviroment = dirname(LITHIUM_APP_PATH) . '/environment.php';
if (!file_exists($enviroment)) {
	$message = "Could not locate enviroment config file at `{$enviroment}`.";
	throw new ConfigException($message);
}
Registry::load($enviroment, array(), 'env');

/**
 * Set environment detection
 */
Environment::is(function(){
	$configured = Registry::get('env.is');
	return $configured ? $configured : 'development';
});

?>