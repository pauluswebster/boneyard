<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
echo $this->html->style(array(
	'mootools-datepicker',
	'chosen',
	'app',
	'fonts'
));

echo $this->html->script(array(
	'mootools-core-1.4.5.js',
	'mootools-more-1.4.0.1.js',
	'mootools-datepicker-yc.js',
	'chosen.min.js',
	'app'
));

?>