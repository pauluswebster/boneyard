<?php
use \lithium\net\http\Router;
use \slicedup_core\configuration\Registry;

Router::connect(Registry::get('slicedup.core.routing.base'), array('library' => 'slicedup', 'controller' => 'dashboard'));