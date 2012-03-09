<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;

/**
 * Add `'behavior'` type configured class types.
 */
Libraries::paths(array(
	'behavior' => array(
		'{:library}\extensions\behavior\{:namespace}\{:class}\{:name}',
		'{:library}\{:namespace}\{:class}\behavior\{:name}' => array('libraries' => 'sli_base')
	),
	'observer' => array(
		'{:library}\extensions\observer\{:namespace}\{:class}\{:name}',
		'{:library}\{:namespace}\{:class}\observer\{:name}' => array('libraries' => 'sli_base')
	)
));

?>