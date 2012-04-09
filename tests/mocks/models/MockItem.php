<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2012, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\tests\mocks\models;

class MockItem extends \sli_base\tests\mocks\models\MockModel {

	protected $_schema = array(
		'id' => array('type' => 'integer'),
		'title' => array('type' => 'string'),
		'class' => array('type' => 'string')
	);
	
	public static function __init() {
		$self = static::_object();
		$schema = $self->_schema;
		parent::__init();
		$self->_schema = $schema;
	}
}

?>