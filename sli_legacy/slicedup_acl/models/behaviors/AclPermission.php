<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_acl\models\behaviors;

use \slicedup_acl\security\Acl;

/**
 * AclPermission
 *
 * @description The Acl Permission Behavior handles automatic CRUD operations of
 * 				Permissions used based on ACO & ARO records for associated records, 
 * 				such as those created by the AclRecord Behavior e.g. Users & ARO's
 *
 * @package 	slicedup_behaviors
 */
class AclPermission extends \slicedup_behaviors\models\behaviors\AssociatedRecord {}
?>