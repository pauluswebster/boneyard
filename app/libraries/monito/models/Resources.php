<?php
namespace monito\models;

class Resources extends AppModel {

	public $belongsTo = array('Connections');

	public static $scaffoldFields = array(
		'name'
	);

	public static function request(\lithium\data\Entity $resource) {
		if (!$resource->connection) {
			throw new \Exception("Resource with id #{$resource->id} does not have valid connection");
		}
		$connectionClass =  $resource->connection->connection;
		$connection = new $connectionClass();
		$settings = unserialize($resource->connection->settings) ?: array();
		$action = $resource->action;
		$params = unserialize($resource->params) ?: array();
		return $connection->request($settings, $action, $params);
	}
}

?>