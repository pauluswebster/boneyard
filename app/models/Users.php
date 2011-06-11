<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

use lithium\util\String;
use slicedup_users\security\CurrentUser;

class Users extends \lithium\data\Model {

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
		'admin'
	);
	
	public static $scaffoldFormFields = array(
		'id',
		'first_name',
		'last_name',
		'email',
		'username',
		'new_password' => array('type' => 'password', 'autocomplete' => 'off'),
		'active' => array('type' => 'hidden', 'value' => 1)
	);
	
	public static function __init(){
		parent::__init();
		static::applyFilter('save', function($self, $params, &$chain) {
			$record = $params['entity'];
			if (!empty($params['data']['new_password'])) {
				$record->new_password = $params['data']['new_password'];
				unset($params['data']['new_password']);
			}
			if (!empty($record->new_password)) {
				$record->password = String::hash($record->new_password);
			}
			$params['entity'] = $record;
			$save = $chain->next($self, $params, $chain);
			if($save && $record->id == CurrentUser::id('users')) {
				$data = $record->data();
				CurrentUser::set('users', $data);
			}
			return $save;
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
	
	protected static function _findFilters() {
		$filters = parent::_findFilters();
		$filters['list'] = function($self, $params, $chain) {
			$name = $self::meta('key');
			foreach ($chain->next($self, $params, $chain) as $entity) {
				$key = $entity->{$name};
				$result[is_scalar($key) ? $key : (string) $key] = $entity->displayName();
			}
			return $result;
		};
		return $filters;
	}

	public static function displayName($record) {
		if (!$record->display_name) {
			$record->display_name = ucfirst($record->first_name);
			$record->display_name.= ' ' . ucfirst(substr($record->last_name, 0, 1));
		}
		return $record->display_name;
	}
	
	public static function fullName($record) {
		if (!$record->full_name) {
			$record->full_name = ucfirst($record->first_name);
			$record->full_name.= ' ' . ucfirst($record->last_name);
		}
		return $record->full_name;
	}
}

?>