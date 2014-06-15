<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_cms\models;

class Branch extends \lithium\data\Model{

	public $hasMany = array(
		'Url'
	);

	public $belongsTo = array(
		'Branch'
	);

	protected $_meta = array(
		'source' => 'cms_branches'
	);
}