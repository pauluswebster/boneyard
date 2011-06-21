<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_util\net\http;

class MediaPaths extends \lithium\net\http\Media {

	public static function defaultPaths($type = null) {
		$handlers = static::_handlers();
		if ($type && isset($handlers[$type]['paths'])) {
 			return $handlers[$type]['paths'];
		}
		return $handlers['default']['paths'];
	}

	public static function setPaths($type, array $templates = array(), $key = null) {
		if (!$config = static::type($type)) {
			return false;
		}
		if ($key) {
			$config['options']['paths'][$key] = $templates;
		} else {
			$config['options']['paths'] = $templates;
		}
		static::type($type, $config['content'], $config['options']);
		return $config['options']['paths'];
	}

	public static function getPaths($type, $key = null) {
		if (!$config = static::type($type)) {
			return false;
		}
		$paths = array();
		if (!empty($config['options']['paths'])) {
			$paths = $config['options']['paths'];
		}
		if ($key) {
			$paths = isset($paths[$key]) ? $paths[$key] : null;
		}
		return $paths;
	}

	public static function addPaths($type, array $templates = array(), $prepend = true) {
		$paths = static::getPaths($type);
		if ($paths === false) {
			return false;
		}
		foreach ($templates as $template => $patterns) {
			$patterns = array_unique((array) $patterns);
			if (isset($paths[$template])) {
				$current = (array) $paths[$template];
				$filter = function($template) use ($current) {
					return !in_array($template, $current);
				};
				if($add = array_filter($patterns, $filter)) {
					$function = $prepend ? 'array_unshift' : 'array_push';
					array_unshift($add, null);
					$add[0] = &$current;
					call_user_func_array($function, $add);
					$paths[$template] = $current;
				}
			} else {
				$paths[$template] = $patterns;
			}
		}
		return static::setPaths($type, $paths);
	}

	public static function removePaths($type, $templates = array(), $preg = false) {
		$paths = static::getPaths($type);
		if ($paths === false) {
			return false;
		}
		if (is_string($templates)) {
			$templates = array_fill_keys(array_keys($paths), $templates);
		}
		foreach ($templates as $template => $patterns) {
			if (isset($paths[$template])) {
				if (is_string($patterns)) {
					if ($preg) {
						$patterns = preg_grep($patterns, $paths[$template]);
					} else {
						$patterns = array($patterns);
					}
				}
				$filter = function($template) use ($patterns) {
					return !in_array($template, $patterns);
				};
				$paths[$template] = array_filter((array) $paths[$template], $filter);
			}
		}
		return static::setPaths($type, $paths);
	}
}

?>