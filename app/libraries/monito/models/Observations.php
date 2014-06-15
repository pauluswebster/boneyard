<?php
namespace monito\models;

use lithium\net\socket\Stream;

use sli_base\util\filters\Behaviors;

class Observations extends AppModel {

	const TEST = 0;

	const PULL = 1;

	const PUSH = 2;

	const PENDING = 0;

	const COMPLETED = 1;

	const FAILED = 2;

	public static function pull(\lithium\data\Entity $resource, $time = null) {
		$id = $resource->id;
		$time = $time ?: time();
		$observation = static::create(array(
			'resource_id' => $id,
			'type' => static::PULL,
			'status' => static::PENDING,
			'time' => $time ?: time()
		));
		$observation->save();
		try {
			$response = $resource->request();
			$observation->response = serialize($response);
			$observation->status = static::COMPLETED;
		} catch (\Exception $e) {
			$observation->status = static::FAILED;
		}
		$observation->save();
		return $observation;
	}

	public static function push(\lithium\data\Entity $resource, $response, $time = null) {}
}

?>