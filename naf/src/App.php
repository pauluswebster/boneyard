<?php

namespace Naf;

use Sli\Filter\Filters;

class App {

	public static $routes = array();

	public static function dispatch() {
		//Request/routing
		$routes = static::$routes;
		$webroot = WWW_ROOT;
		$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
		$wl = strlen(WEBROOT_DIR);
		if (substr($baseUrl, 0-$wl, $wl) == WEBROOT_DIR) {
			$baseUrl = substr($baseUrl, 0, 0-$wl);
		}
		$baseUrl = rtrim($baseUrl, '/') . '/';
		$here = '';
		if (!empty($_REQUEST['url'])) {
			$here = rtrim($_REQUEST['url'], '/');
		}

		//View/render
		$views = APP . '/Template/';
		$elements = "{$views}Element/";
		$layouts = "{$views}Layout/";

		$params = compact('here', 'webroot', 'baseUrl', 'routes', 'views', 'elements', 'layouts');
		$render = function($self, $params){
			$format = 'html';
			extract($params);
			$error = false;
			if (isset($routes[$here])) {
				$page = $routes[$here];
			} else {
				$page = 'error';
				$error = true;
			}

			$url = function($url = '') use($baseUrl) {
				return $baseUrl . ltrim($url, '/');
			};

			$asset = function($file) use($url){
				//copy into new loc
				if (file_exists($file)) {
					$file .= '?' . filemtime($file);
				}
				return $url($file);
			};

			$img = function($file) use($url){
				return $url('img/' . ltrim($file, '/'));
			};

			$element = function($file) use($elements, $format, $baseUrl, $here, $url, $asset, $img){
				$elementFile = "{$elements}{$file}.{$format}.php";
				include $elementFile;
			};

			$viewContent = '';
			$content = function() use(&$viewContent) {
				echo $viewContent;
			};
			$layout = 'default';

			$viewFile =  "{$views}Pages/{$page}.{$format}.php";
			if (!file_exists($viewFile)) {
				$page = 'error';
				$viewFile = "{$views}Pages/{$page}.{$format}.php";
			}

			ob_start();
			include $viewFile;
			$viewContent = ob_get_contents();
			ob_end_clean();
			$layoutFile = "{$layouts}{$layout}.{$format}.php";
			include $layoutFile;
			if ($error) {
				$cache = false;
			} elseif (!isset($cache)) {
				$cache = true;
			}
			//return $cache;
		};

		ob_start();
		$cache = Filters::run(__METHOD__, $params, $render);
		$output = ob_get_contents();
		ob_end_clean();

		if ($cache) {
			$pageFilename = $here ?: 'default';
			$pageFile = "{$webroot}www_cache/{$pageFilename}.html";
			$pageFileDir = dirname($pageFile);
			if (!is_dir($pageFileDir)) {
				@mkdir($pageFileDir, 0755, true);
			}
			if (is_dir($pageFileDir) && is_writable($pageFileDir)) {
				file_put_contents($pageFile, $output . "<!-- '{$pageFilename}' generated on " . date('r') . ' -->');
			}
		}

		echo $output;
	}
}

?>