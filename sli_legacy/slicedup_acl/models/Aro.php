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
 * @description Aro model class. Access request objects (ARO's) are ACL records 
 * 				that reference requesting resources/objectes within our application.
 *
 * @package 	slicedup_acl
 */
class Aro extends \lithium\data\Model{

	public $hasMany = array(
		'Permission' => array(
			'class' => 'Permission',
			'key' => 'aro_id'
		)
	);
}

?>