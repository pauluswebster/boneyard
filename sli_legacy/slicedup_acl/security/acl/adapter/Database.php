<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace slicedup_acl\security\acl\adapter;

/**
 * Database
 *
 * @description Database Acl adapter.
 * 				
 *
 * @package 	slicedup_acl
 */
class Database extends \lithium\core\Object implements \slicedup_acl\security\acl\adapter\AclInterface{

	public static function allow(){}
	
	public static function deny(){}
	
	public static function check(){}
	
	public static function createAro(){}
	
	public static function createAco(){}
}

?>