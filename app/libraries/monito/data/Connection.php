<?php

namespace  monito\data;

interface Connection {

	public function request($config, $action, $params);

}