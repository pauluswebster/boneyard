<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_acl\security;

class Permission extends \lithium\core\StaticObject {

	protected $_permissions = array(
		'create' => 1,
		'read' => 2,
		'update' => 4,
		'delete' => 8
	);

}