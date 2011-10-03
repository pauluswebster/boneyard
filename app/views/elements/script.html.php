<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
echo $this->html->style(array(
	'/js/mootools-datepicker/datepicker_dashboard/datepicker_dashboard'
));

echo $this->html->script(array(
	'mootools-core-1.4.0-full-nocompat.js',
	'mootools-more/Types/Object.Extras',
	'mootools-more/Locale/Locale',
	'mootools-more/Locale/Locale.en-US.Date',
	'mootools-more/Types/Date',
	'mootools-datepicker/Locale.en-US.DatePicker',
	'mootools-datepicker/Picker',
	'mootools-datepicker/Picker.Attach',
	'mootools-datepicker/Picker.Date',
	'app'
));

?>