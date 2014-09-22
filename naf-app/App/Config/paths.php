<?php

/**
 * Use the DS to separate the directories in other defines
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * The full path to the directory which holds "App", WITHOUT a trailing DS.
*/
define('ROOT', dirname(dirname(__DIR__)));

/**
 * The actual directory name for the "App".
*/
define('APP_DIR', basename(dirname(__DIR__)));

/**
 * The name of the webroot dir.  Defaults to 'webroot'
*/
define('WEBROOT_DIR', 'webroot');

/**
 * Path to the application's directory.
*/
define('APP', ROOT . DS . APP_DIR . DS);

/**
 * File path to the webroot directory.
*/
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);