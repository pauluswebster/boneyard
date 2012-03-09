<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

/**
 * The `Encoded` model behavior.
 *
 * This behavior applies encode/decode filters to configured record fields on
 * save and find operations.
 *
 * By default is supports base64 & url encoding, you can add additional
 * formats by calling Encoded::modifiers().
 *
 * @see sli_base\data\model\behavior\Modified::modifiers()
 *
 * Apply to your model classes, specifying fields and the encoding format.
 *
 * For example to store the field `'query'` url encoded and the field `'data'`
 * base64 encoded:
 * {{{
 * Encoded::apply($model, array('fields' => array(
 * 	'query' => 'url',
 * 	'data' => 'base64'
 * )));
 * //or
 * Encoded::apply($model, array(
 * 	'url' => 'query',
 * 	'base64' => 'data'
 * ));
 * }}}
 *
 * These fields will then be encoded in the corresponding format on save,
 * and decoded back into full data format on find.
 *
 * @see sli_base\data\model\behavior\Modified
 * @see sli_base\core\Behavior
 * @see sli_base\core\Behaviors
 */
class Encoded extends \sli_base\data\model\behavior\Modified {

	protected static $_modifiers = array(
		'base64' => array(
			'save' => 'base64_encode',
			'find' => 'base64_decode'
		),
		'url' => array(
			'save' => 'urlencode',
			'find' => 'urldecode',
		)
	);

	protected static $_modifierOnly = true;
}

?>