<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

use sli_base\storage\Registry;
use app\util\TimeZones;
use app\security\User;

class Tasks extends WorkUnit {

	public static function getScaffoldFormFields(){
		$user = User::instance('default');
		$fields = array(
			'job_id' => array(
				'type' => 'select',
				'list' => array()
			),
			'title',
			'description' => array('type' => 'textarea'),
			'due' => array(
				'class' => 'date-picker',
				'data-format' => Registry::get('app.date.js-long')
			),
			'timezone' => array(
				'type' => 'select',
				'list' => TimeZones::get() + array(
					'My TimeZones' => $user->timezones()
				)
			)
		);
		return array(
			'Task' => compact('fields')
		);
	}
}

?>