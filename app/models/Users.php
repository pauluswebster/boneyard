<?php
namespace app\models;

use lithium\util\String;
use lithium\security\Password;
use sli_base\util\filters\Behaviors;

class Users extends AppModel {

	public $validates = array(
		'first_name' => 'Please enter a first name',
		'last_name' => 'Please enter a last name',
		'username' => 'Please enter a username',
		'email' => array(
			array('notEmpty', 'message' => 'Please enter an email address'),
			array('email', 'message' => 'Email address is not valid.')
 	));

	protected $_meta = array(
		'title' => 'username'
	);

	public static $scaffoldFields = array(
		'first_name',
		'last_name',
		'email',
	);

	public static function __init(){
		parent::__init();
		static::_applyFilters();
	}

	public static function getScaffoldFormFields(){
		$fields = array(
			'id',
			'first_name',
			'last_name',
			'email',
			'username',
			'new_password' => array('type' => 'password', 'autocomplete' => 'off'),
			'active' => array('type' => 'hidden', 'value' => 1),
			'admin' => array('type' => 'checkbox'),
		);
		return $fields;
	}

	protected static function _applyFilters() {
		Behaviors::apply(__CLASS__, array(
			'Serialized' => array(
				'fields' => array(
					'settings' => 'json'
				)
			)
		));

		static::applyFilter('save', function($self, $params, &$chain) {
			$record = $params['entity'];
			if (!empty($params['data']['new_password'])) {
				$record->new_password = $params['data']['new_password'];
				unset($params['data']['new_password']);
			}
			if (!empty($record->new_password)) {
				$record->password = Password::hash($record->new_password);
			}
			$params['entity'] = $record;
			return $chain->next($self, $params, $chain);
		});

		static::applyFilter('create', function($self, $params, &$chain) {
			$record = $chain->next($self, $params, $chain);
			if (!empty($record->password)) {
				$record->new_password = $record->password;
			} else {
				$record->new_password = $record->password = bin2hex(String::random(4));
			}
			$record->token = String::uuid();
			return $record;
		});
	}
}

?>