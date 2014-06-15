<?php

namespace monito\extensions\command;

use monito\models\Resources;
use sli_jobs\models\Jobs;

class ObservationPullQueue extends \lithium\console\Command {

	public function run($command = null) {
		$now = time();
		$lookahead = $now + 60;
		$span = $lookahead + 60;
		$resources = Resources::all(array(
			'conditions' => array(
				'pull'=> 1,
				'pull_next' => array('<' => $span),
// 				'or' => array(
// 					array('pull_next' => array('<' => $lookahead)),
// 					array('and' => array('pull_next' => array('>=' => $lookahead, '<' => $span)))
// 				)
			)
		));
		foreach ($resources as $resource) {
			$resource->pull_next = $resource->pull_next ?: $now;
			if ($resource->pull_next < $now) {
				$resource->pull_next = $now;
			}
			$resource->pull_next += $resource->pull_frequency;
			$args = $resource->key() + array('time' => $resource->pull_next);
			$job = array(
				'command' => 'ObservationPull',
				'args' => $args,
				'time' => $resource->pull_next
			);
			$resource->save();
			Jobs::queue('default', $job);
		}
	}
}

?>