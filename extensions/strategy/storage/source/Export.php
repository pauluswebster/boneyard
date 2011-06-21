<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\extensions\strategy\storage\source;

/**
 * The storage.source.strategy `Export` class.
 */
class Export extends \lithium\core\Object {

	protected $_data;

	protected $_loadTime;

	public function __construct(array $config = array()) {
		$defaults = array('var' => 'config', 'flatten' => false);
		$config += $defaults;
		return parent::__construct($config);
	}

	public function read($data, $options){
		$this->_loadTime = $options + $this->_config;
		$this->_data = $data;
		$result = $this->_eval();
		unset($this->_data, $this->_loadTime);
		return $result;
	}

	public function write($data, $options){
		$options += $this->_config;
		$export = var_export($data, true);
		$content = "<?php\n//Output by ".__CLASS__." at " . time();
		$content.= "\n\${$options['var']} = $export;";
		return $content;
	}

	protected function _eval(){
		if(@eval('?>' . $this->_data) !== false){
			if (isset(${$this->_loadTime['var']})) {
				return ${$this->_loadTime['var']};
			}
		}
	}
}

?>