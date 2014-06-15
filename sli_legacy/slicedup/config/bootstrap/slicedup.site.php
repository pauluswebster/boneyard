<?php
/**
 * This file is specific to the app Slicedup is currently managing
 * 
 * Definitions in this file are the Slicedup default configuration values.
 * To override these for your app create the file slicedup.site.php in you app/config/bootstrap
 * directory and and create a $config array with the keys you wish to overwrite as needed.
 */

$domain = 'http://' . $_SERVER['HTTP_HOST'];

$config = array(
	'app' => array(
		/**
		 * App path
		 */
		'path' => LITHIUM_APP_PATH,
		
		'routing' => array(
			/**
			 * App domain
			 */
			'domain' => $domain,
			/**
			 * App base url
			 */
			'base' => ''
		)
	)
);