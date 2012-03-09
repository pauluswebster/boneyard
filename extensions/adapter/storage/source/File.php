<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\extensions\adapter\storage\source;

use SplFileInfo;

/**
 * The storage.storage.adapter `File` class.
 */
class File extends \lithium\core\Object {

	public function read($path, array $options = array()){
		return function($self, $params, $chain) {
			extract($params);
			foreach ((array) $path as $_path) {
				$file = new SplFileInfo($_path);
				if ($file->isFile() && $file->isReadable())  {
					return file_get_contents($_path);
				}
			}
		};
	}

	public function write($path, $data, array $options = array()){
		return function($self, $params, $chain) {
			extract($params);
			return !!file_put_contents($path, $data);
		};
	}

	public function delete($path, array $options = array()){
		return function($self, $params, $chain) {
			extract($params);
			$file = new SplFileInfo($path);
			if ($file->isFile() && $file->isReadable())  {
				return @unlink($path);
			}
		};
	}
}

?>