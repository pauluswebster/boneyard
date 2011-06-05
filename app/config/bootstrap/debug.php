<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

function p($var){
	echo '<pre>' . print_r($var, 1) . '</pre>';
}

function v(){
	$args = func_get_args();
	ob_start();
	call_user_func_array('var_dump', $args);
	$out = ob_get_contents();
	ob_end_clean();
	p($out);
}

function h($var){
	if(is_array($var)){
		foreach($var as &$v) $v = h($v);
		return $var;
	}
	return htmlentities($var);
}

function d($var){
	p($var);
	die;
}