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
 * AclRecord
 *
 * @description The Acl Record Behavior handles automatic CRUD operations of
 * 				ACO & ARO records for associated records, e.g. Users & ARO's
 *
 * @package 	slicedup_behaviors
 */
class AclRecord extends \slicedup_behaviors\models\behaviors\AssociatedRecord {}
?>