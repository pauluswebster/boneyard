<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;

/**
 * Detect presence of Assetic, if not found include it from the library path
 * in it's default full deployment.
 *
 * @link git://github.com/kriswallsmith/assetic.git
 */
if (!Libraries::get('assetic')) {
	Libraries::add('assetic', array(
		'path' => LITHIUM_LIBRARY_PATH . '/assetic/src',
		'prefix' => false,
	));
}

Libraries::paths(array(
	'asseticFilter' => array(
		'{:library}\Filter\{:name}Filter' => array('library' => 'assetic'),
		'{:library}\Filter\GoogleClosure\{:name}Filter' => array('library' => 'assetic'),
		'{:library}\Filter\Sass\{:name}Filter' => array('library' => 'assetic'),
		'{:library}\Filter\Yui\{:name}Filter' => array('library' => 'assetic'),
		'{:library}\extensions\Assetic\Filter\{:name}Filter',
	),
	'asseticAsset' => array(
		'{:library}\Asset\{:name}Asset' => array('library' => 'assetic'),
		'{:library}\extensions\Assetic\Asset\{:name}\Asset'
	)
));
?>