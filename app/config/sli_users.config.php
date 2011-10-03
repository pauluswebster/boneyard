<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$config = array(
	'model' => array(
		'class' =>	'\app\models\Users'
	),
	'controller' => array(
		'library' => 'app',
		'class' => 'app\controllers\UsersController',
		'actions' => array(
			'register' => false
		)
	),
	'routing' => array(
		'base' => '',
		'loginRedirect' => '/',
		'logoutRedirect' => '/login'
	),
	'persist' => array(
		'name' => 'cookie'
	)
);
?>