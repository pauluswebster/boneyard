<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$tz = date_default_timezone_get();

$config = array(
	'actions' => array(
		'public' => array(
			'users::login',
			'users::password_reset'
		),
		'admin' => array(
			'users::add',
			'users::delete',
			'users::index'
		)
	),
	'date' => array(
		'long' => 'D d M, H:i',
		'js-long' => '%a %d %b, %H:%M'
	),
	'timezone' => array(
		'default' => $tz
	),
	'currency' => array(
		'default' => 'NZD'
	),
	'scaffold' => array(
		'all' => false,
		'scaffold' => array(
			'jobs' => array(
				'actions' => array(
					'index',
					'add',
					'edit',
					'delete'
				)
			),
			'users' => array(
				'actions' => array(
					'index',
					'add',
					'edit',
					'delete'
				)
			),
			'tasks' => array(
				'actions' => array(
					'index',
					'add',
					'edit',
					'delete'
				)
			)
		)
	)
);
?>