<?php
use lithium\core\Libraries;
/**
 * @todo integrate with library registry
 */


///**
// * Loads in default Slicedup modules available
// */
//Libraries::paths(array(
//	'libraries' => array_merge(Libraries::paths('libraries'), array(SLICEDUP_MODULES_PATH . '{:name}'))
//));
//
//foreach (glob(SLICEDUP_MODULES_PATH . "*", GLOB_ONLYDIR) as $module) {
//    $moduleName = basename($module);
//	Libraries::add('slicedup_' . $moduleName, array('path' => $module));
//}