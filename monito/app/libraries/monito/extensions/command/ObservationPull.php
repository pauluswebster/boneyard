<?php

namespace monito\extensions\command;

use monito\models\Resources;
use monito\models\Observations;

class ObservationPull extends \lithium\console\Command {

	public function run($command = null) {
		extract($this->request->params);
		if (!isset($id)) {
			throw new \Exception("Resource id not specified");
		}
		if (!isset($job)) {
			throw new \Exception("Job id not specified");
		}
		$resource = Resources::first($id, array(
			'with' => array('Connections')
		));
		if (!$resource) {
			throw new \Exception("Resource with id #{$id} does not exist");
		}
		Observations::pull($resource, @$time);
	}
}

?>