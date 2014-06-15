<?php
/**
 * This file is specific to the app Slicedup is installed in, and not the app(s) it manages
 * 
 * Definitions in this file are the Slicedup default configuration values.
 * To override these for your install create the file slicedup.php in you app/config/bootstrap
 * directory and and create a $config array with the keys you wish to overwrite as needed.
 */
$domain = 'http://' . $_SERVER['HTTP_HOST'];

$config = array(
	/**
	 * Location of slicedup modules e.g. slicedup_cms
	 */
	'modulePaths' => array(dirname(dirname(__DIR__)) . '/modules/'),
	/**
	 * Slicedup managing multiple apps
	 */
	'multipleApp' => false,
	
	'routing' => array(
		/**
		 * Slicedup domain
		 */
		'domain' => $domain,
		/**
		 * Slicedup base url
		 */
		'base' => '/sli'
	)
);