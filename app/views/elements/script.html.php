<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
echo $this->html->style(array(
	'app',
	'fonts',
	'mootools-datepicker'
));

echo $this->html->script(array(
	'mootools-core-1.4.0-full-nocompat-yc.js',
	'mootools-more-1.4.0.1-nocompat-yc.js',
	'mootools-datepicker-yc.js',
	'app'
));

?>