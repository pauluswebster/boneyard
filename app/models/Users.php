<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

use lithium\util\String;

class Users extends \lithium\data\Model {

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
	
	public static function displayName($record) {
		if ($record->display_name) {
			return $record->display_name;
		}
		$record->display_name = ucfirst($record->first_name);
		$record->display_name.= ' ' . substr($record->last_name, 0, 1);
		return $record->display_name;
	}
}

?>