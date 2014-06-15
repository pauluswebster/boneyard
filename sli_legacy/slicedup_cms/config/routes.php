<?php
use \lithium\net\http\Router;
use \slicedup_core\configuration\Registry;
/**
 * @todo Properly hanlde this via Library/Registry
 */
$baseUrl = Registry::get('slicedup.core.routing.base');
Router::connect($baseUrl . '/cms', array('library' => 'slicedup_cms', 'controller' => '\slicedup_cms\controllers\CmsController'));
Router::connect($baseUrl . '/cms/{:controller}/{:action}/{:id:[0-9]+}',array('library' => 'slicedup_cms'));
Router::connect($baseUrl . '/cms/{:controller}/{:action}/{:args}', array('library' => 'slicedup_cms'));