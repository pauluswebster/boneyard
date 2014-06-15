<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_acl\models;

/**
 * Permission
 *
 * @description Permission model class. Permissions are ACL records that define
 * 				access rules for ARO's to ACO's within our ACL system.
 * 				
 *
 * @package 	slicedup_acl
 */
class Permission extends \lithium\data\Model{

	protected $_meta = array(
		'source' => 'acl_permissions'
	);

	public $belongsTo = array(
		'Aco' => array(
			'class' => 'Aco',
			'key' => 'aco_id'
		),
		'Aro' => array(
			'class' => 'Aro',
			'key' => 'aro_id'
		) 
	);	
}

?>