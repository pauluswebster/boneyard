<?php

namespace centrifuge\models;

use lithium\util\Set;

class Projects extends Centrifuge {
	
	public $validates = array(
		'title' => 'please enter a title'
	);
	
	protected static $_actsAs = array(
		'ManyToMany' => array(
			'bind' => array('Staff')
		)
	);
	
	public static function getScaffoldFormFields($binding) {
		$fields = parent::getScaffoldFormFields($binding);
		$pricing = array_intersect_key($fields, array_fill_keys(array('fee', 'fixed', 'currency', 'currency_rate'), null));
		$exlcude = array_intersect_key($fields, array_fill_keys(array('completed', 'started', 'timezone'), null));	
		$fields = array_diff_key($fields, $pricing, $exlcude);
		
		$staffList = Staff::all(array(
			'fields' => array('id', 'first_name', 'last_name'),
			'order' => 'first_name'
		));
		if (count($staffList)) {
			$staffList = Set::combine($staffList->to('array', array('indexed' => false)), '/id', array('{0} {1}', '/first_name', '/last_name'));
		} else {
			$staffList = array();
		}
		
		$staff = $binding->exists() ? $binding->staff(array('find' => 'list')) : array();
		if ($staff) {
			$staff = array_keys($staff);
		}
		
		$fieldsets = array(
			'Project' => compact('fields'),
			'Staff' => array(
				'fields' => array(
					'staff' => array(
						'type' => 'select',
						'multiple' => true,
						'list' => $staffList,
						'value' => $staff
					),
					'staff[]' => array(
						'type' => 'hidden'
					)
				)
			),
			'Pricing' => array(
				'fields' => $pricing
			)
		);
		
		return $fieldsets;
	}
}

?>