<?php

namespace monito\data\connections\network;

use lithium\net\socket\Stream;
use lithium\net\http\Request;
use lithium\core\NetworkException;

class Http extends \lithium\core\Object implements \monito\data\Connection {

	public function request($settings, $action, $params) {
		$defaults = array(
			'port' => 80
		);
		$config = array(
			'path' => $action,
			'persistent' => false,
			'timeout' => 10,
			'classes' => array(
				'request' => 'lithium\net\http\Request',
				'response' => 'lithium\net\http\Response'
			)
		);
		$config = array_merge($defaults, $settings, $params, $config);
		$stream = new Stream($config);
		if ($stream->open()) {
			$request = new Request($config);
			if ($response = $stream->send($request)) {
				return array(
					'meta' => array(
						'headers' => $response->headers(),
						'type' => $response->type(),
						'status' => $response->status,
						'cookies' => $response->cookies,
						'encoding' => $response->encoding
					),
					'content' => $response->body
				);
			}
		}
	}
}

?>