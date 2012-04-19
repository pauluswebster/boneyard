<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$config = array(
	'staging' => array(
		'name' => 'staging',
		'model' => array(
			'class' =>	'\app\models\Users'
		),
		'controller' => array(
			'library' => 'app',
			'class' => 'app\controllers\UsersController',
			'actions' => array(
				'register' => false,
				'password_reset' => false
			)
		),
		'routing' => array(
			'base' => '/staging'
		),
		'auth' => array(
			'scope' => array(
				'admin' => true
			)
		),
		'persist' => array(
			'storage' => array(
				'name' => 'cookie',
			)
		)
	)
);
?>