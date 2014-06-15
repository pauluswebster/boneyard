<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_acl\models;

/**
 * Aco
 *
 * @description Aco model class. Access control objects (ACO's) are records 
 * 				that reference controlled resources/objectes within our application.
 *
 * @package 	slicedup_acl
 */
class Aco extends \lithium\data\Model{

	public $hasMany = array(
		'Permission' => array(
			'class' => 'AclPermission',
			'key' => 'aco_id'
		)
	);
}

?>