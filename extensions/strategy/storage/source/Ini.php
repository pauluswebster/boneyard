<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\extensions\strategy\storage\source;

/**
 * The storage.source.strategy `Ini` class.
 */
class Ini extends \lithium\core\Object {

	public function __construct(array $config = array()) {
		$defaults = array('sections' => false, 'raw' => false);
		$config += $defaults;
		return parent::__construct($config);
	}

	public function read($data, $options){
		$options += $this->_config;
		$sections = (boolean) $options['sections'];
		$mode = $options['raw'] ? INI_SCANNER_RAW : INI_SCANNER_NORMAL;
		return parse_ini_string($data, $sections, $mode);
	}

	public function write($data){}
}

?>