<?php
use lithium\util\collection\Filters;
use slicedup_core\configuration\LibraryRegistry;
use slicedup_cms\models\Url;


//temp hack in config to lib reg
LibraryRegistry::set('slicedup_cms', array(
	'default' => array(
		'routing' => array(
			'match' => '.*'
		)
	)
));

Filters::apply('\lithium\action\Dispatcher', 'run', function($self, $params, $chain){
	$url = $params['request']->url;
	$config = LibraryRegistry::get('slicedup_cms');
	foreach ($config as $_config) {
		if (preg_match("#{$_config['routing']['match']}#", $url)) {
			$cmsNode = Url::node($url);
		}
	}
	return $chain->next($self, $params, $chain);
});