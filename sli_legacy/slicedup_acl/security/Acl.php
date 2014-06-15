<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace slicedup_acl\security;

use \slicedup_acl\models\Aro;
use \slicedup_acl\models\Aco;
use \slicedup_acl\models\Permission;

/**
 * Acl
 *
 * @description ACL class.
 *
 * @package 	slicedup_acl
 */
class Acl extends \lithium\core\Adaptable{
	
	protected static $_configurations = array();

	protected static $_adapters = 'adapter.security.acl';
	
	public static function allow(){}
	
	public static function deny(){}
	
	public static function check(){}
	
	public static function createAro(){}
	
	public static function createAco(){}
	
}

?>