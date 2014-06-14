<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_cms\controllers;

class LeavesController extends \lithium\action\Controller {

	public $scaffold = array();

	public function view() {
		d($this->request->params['leaf']->title);
		return $this->request->params['leaf']->title;
	}
}
?>