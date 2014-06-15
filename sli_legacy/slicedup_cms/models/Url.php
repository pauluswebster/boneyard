<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_cms\models;

class Url extends \lithium\data\Model{

	public $belongsTo = array(
		'Branch'
	);

	protected $_meta = array(
		'source' => 'cms_urls'
	);

	public static function node($requestUrl){
		$hash = md5($requestUrl);
		$url = self::find('first');
//		v(compact('requestUrl', 'hash', 'url'));
	}
}