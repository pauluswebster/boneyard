<?php
namespace sli_jobs\models;

use sli_base\util\filters\Behaviors;

class Jobs extends \lithium\data\Model {

	const PENDING = 0;

	const DEQUEUED = 1;

	const RUNNING = 2;

	const COMPLETED = 3;

	const ERROR = 4;

	const FAILED = 5;

	public static function __init(){
		Behaviors::apply(get_called_class(), 'Timestamped');
	}

	public static function queue($queue, $command, array $params = array()) {
		if (is_array($command)) {
			$params = $command + $params;
		} else {
			$params['command'] = $command;
		}
		unset($command);
		extract($params, EXTR_SKIP);
		if (strpos($command, '::')) {
			list($command, $action) = explode('::', $command, 2);
		}
		$action = @$action ?: 'run';
		$args = @$args ? serialize($args) : null;
		$attempts = @$attempts ?: 0;
		$status = @$status ?: static::PENDING;
		$worker = @$worker ?: null;
		$time = @$time ?: time();
		$priority = null;//@todo
		$data = compact('queue', 'command', 'action', 'args', 'status', 'attempts', 'worker', 'time');
		$job = static::create($data);
		if ($job->save()) {
			return $job;
		}
	}

	public static function dequeue($queue, $worker, $time = null) {
		$time = $time ?: time();
		$conditions = array(
			'time' => array('<=' => $time),
			'queue' => $queue,
			'status' => static::PENDING,
			'worker' => null
		);
		$order = array(
			//'priority' => 'desc',//@todo
			'time' => 'asc'
		);
		return static::_dequeue(compact('conditions', 'order'), $worker);
	}

	/**
	 * @todo select / update locking proper (adapters whatever)
	 */
	protected static function _dequeue($params, $worker) {
		$connection = static::connection();

		//txns on pdo
		if ($connection->connection instanceOf \PDO) {
			$connection->connection->beginTransaction();
		}

		//SELECT...FOR UPDATE custom query support on MySql & PostgreSql
		if ($connection instanceOf \lithium\data\source\database\adapter\MySql
			|| $connection instanceOf lithium\data\source\database\adapter\PostgreSql) {
			$params['limit'] = '1';
			$options = $params + array('type' => 'read', 'model' => get_called_class());
			$query = static::_instance('query', $options);
			$data = $query->export($connection);
			$sql = $connection->renderCommand($data['type'], $data);
			$sql = str_replace('1;', '1 FOR UPDATE;', $sql);
			$result = $connection->invokeMethod('_execute', array($sql));
			$recordSet = static::create(array(), compact('query', 'result') + array(
				'class' => 'set', 'defaults' => false
			));
			$job = $recordSet->current();
		} elseif ($connection instanceOf \lithium\data\source\MongoDb) {
			//@todo we can prob do something for mongo here too
			//@link http://docs.mongodb.org/manual/reference/method/db.collection.update/
			//@see multi = false
		}

		if ($job  = (isset($job) ? $job : static::first($params))) {
			$job->status = static::DEQUEUED;
			$job->worker = $worker;
			$job->save();
		}

		//txns on pdo
		if ($connection->connection instanceOf \PDO) {
			if (!$connection->connection->commit()) {
				$connection->connection->rollBack();
				return;
			}
		}
		return $job;
	}

	public static function pending($entity) {
		$entity->status = static::PENDING;
		return $entity;
	}

	public static function running($entity) {
		$entity->status = static::RUNNING;
		$entity->attempts++;
		return $entity;
	}

	public static function completed($entity) {
		$entity->status = static::COMPLETED;
		return $entity;
	}

	public static function error($entity) {
		$entity->status = static::ERROR;
		return $entity;
	}

	public static function failed($entity) {
		$entity->status = static::FAILED;
		return $entity;
	}
}