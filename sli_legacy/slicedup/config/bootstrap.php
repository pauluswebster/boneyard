<?php
use lithium\core\Libraries;
use \slicedup_core\configuration\Registry;
use \slicedup_core\configuration\LibraryRegistry;

/**
 * Slicedup base libraries
 */
Libraries::add('slicedup_core');
Libraries::add('slicedup_scaffold');
Libraries::add('slicedup_cms');

/**
 * Configure slicedup_users
 */
LibraryRegistry::add('slicedup_users', 'sdu', array(
	'routing' => array(
		'base' => '/sli',
		'loginRedirect' => '/sli',
		'logoutRedirect' => '/sli/login',
	),
	'controller' => array(
		'actions' => array(
			'register' => false
		)
	),
	'persist' => array(
		'name' => '__sdu'
	)
));

/**
 * Slicedup config
 */
$baseConfigPaths = array(
	__DIR__ . '/bootstrap/slicedup.php',
	LITHIUM_APP_PATH . '/config/bootstrap/slicedup.php',
);
$slicedupBase = Registry::load($baseConfigPaths, null, array('merge' => true, 'path' => 'slicedup.core'));
/**
 * Load config for the currently managed app
 */
if ($slicedupBase['multipleApp']) {
	/*
	 * not quite decided on how we'll manage this yet
	 */
} else {
	$appConfigPaths = array(
		__DIR__ . '/bootstrap/slicedup.site.php',
		LITHIUM_APP_PATH . '/config/bootstrap/slicedup.site.php',
	);
	$slicedupApp = Registry::load($appConfigPaths, null, array('path' => 'slicedup', 'merge' => true));
}

/**
 * Slicedup modules this will be app specific but just handle here for now
 */
//if (!@include SLICEDUP_SITE_APP_PATH . '/config/bootstrap/slicedup.modules.php') {
	require __DIR__ . '/bootstrap/slicedup.modules.php';
//}