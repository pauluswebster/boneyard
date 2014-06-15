<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_assetic\extensions\Assetic;

use lithium\core\Libraries;
use Assetic\AssetManager;

class Package extends \lithium\core\Object {

	protected $_assetManager = null;

	protected function _init() {
		parent::_init();
		$this->assetManager = new AssetManager();
	}

	public function add($name, $path = null, $options = array()) {
		if (!isset($path)) {
			$path = $name;
		}
		$this->assetManager->set($name, $this->_asset($path, $options));
	}

	protected function _asset($path, $options) {
		extract($options);
		if (!isset($asset)) {
			switch(true) {
				case substr($path, -1) == '*':
					$asset = 'Glob';
					break;
				case substr($path, 4) == 'http':
					$asset = 'Http';
					break;
				default;
					$asset = 'File';
			}
		}
		if (is_object($asset)) {
			return $asset;
		} else {
			$asset = Libraries::locate('asseticAsset', $type);
			return new $asset($path);
		}
	}

}
?>