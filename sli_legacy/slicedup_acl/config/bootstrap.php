<?php
use lithium\core\Libraries;

/**
 * Add adapter path for Acl
 */
$adapterPaths = Libraries::paths('adapter');
$adapterPaths[] = '\slicedup_acl\{:namespace}\{:class}\adapter\{:name}';
$adapterPaths['\slicedup_acl\{:namespace}\{:class}\adapter\{:name}'] = array('libraries' => 'slicedup_acl');
Libraries::paths(array('adapter' => $adapterPaths));