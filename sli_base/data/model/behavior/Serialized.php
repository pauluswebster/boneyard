<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

/**
 * The `Serialized` model behavior.
 *
 * This behavior applies serialization filters to configured record fields on
 * save and find operations.
 *
 * By default is supports json & php serialization, you can add additional
 * formats by calling Serialized::modifiers().
 *
 * @see sli_base\data\model\behavior\Modified::modifiers()
 *
 * Apply to your model classes, specifying fields and the serialization format.
 *
 * For example to store the field `'settings'` as json and the field `'data'`
 * serialized:
 * {{{
 * Serialized::apply($model, array('fields' => array(
 * 	'settings' => 'json',
 * 	'data' => 'serialize'
 * )));
 * //or
 * Serialized::apply($model, array(
 * 	'json' => 'settings',
 * 	'serialize' => 'data'
 * ));
 * }}}
 *
 * These fields will then be serialized in the corresponding format on save,
 * and expanded back into full data format on find.
 *
 * @see sli_base\data\model\behavior\Modified
 * @see sli_base\core\Behavior
 * @see sli_base\core\Behaviors
 */
class Serialized extends \sli_base\data\model\behavior\Modified {

	protected static $_modifiers = array(
		'serialize' => array(
			'save' => 'serialize',
			'find' => 'unserialize'
		),
		'json' => array(
			'save' => 'json_encode',
			'find' => array('json_decode', true),
		),
		'jsonObj' => array(
			'save' => 'json_encode',
			'find' => 'json_decode'
		)
	);

	protected static $_modifierOnly = true;
}

?>