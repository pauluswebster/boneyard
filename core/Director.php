<?php

namespace sli_jobs\core;

use lithium\core\Libraries;

class Director extends \lithium\core\StaticObject {

	public static function trackActivity($queue, $worker) {
		$file = static::_workerFile($queue, $worker);
		return touch($file);
	}

	public static function lastActivity($queue, $worker = null) {
		$file = static::_workerFile($queue, $worker);
		if (file_exists($file)) {
			return filemtime($file);
		}
		return false;
	}

	public static function stopSent($queue, $worker = null) {
		$queueFile = static::_stopFile($queue);
		if (file_exists($queueFile)) {
			return true;
		}
		if ($worker) {
			$workerFile = static::_stopFile($queue, $worker);
			if (file_exists($workerFile)) {
				return true;
			}
		}
		return false;
	}

	public static function stopSend($queue, $worker = null) {
		$stopFile = static::_stopFile($queue, $worker);
		return touch($stopFile);
	}

	public static function stopRelease($queue, $worker = null) {
		$stopFile = static::_stopFile($queue);
		if (file_exists($stopFile)) {
			return unlink($stopFile);
		}
		return true;
	}

	protected function _workerFile($queue, $worker) {
		$path = Libraries::get(true, 'resources') . '/tmp/cache/';
		$file = "{$path}queue.{$queue}.{$worker}";
		return $file;
	}

	protected function _stopFile($queue, $worker = null) {
		$path = Libraries::get(true, 'resources') . '/tmp/cache/';
		$file = "{$path}stop.queue.{$queue}";
		if ($worker) {
			$file.= ".{$worker}";
		}
		return $file;
	}
}
?>