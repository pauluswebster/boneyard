<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2012, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\models;

class MockItemTwo extends MockItemOne {

	protected $_schema = array(
		'id' => array('type' => 'integer'),
		'color' => array('type' => 'string')
	);
}

?>