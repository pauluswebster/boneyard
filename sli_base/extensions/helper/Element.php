<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2012, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\extensions\helper;


/**
 * The `Element` helper class wraps the rendering of elements
 * with some convenience functionailty added to extend the usbaility
 * of elements including caching.
 *
 * @todo caching
 * @todo greater configurability to render other template types
 * @todo check the sanity of the render call, need anythign else
 * @todo look at lithium\template\view\Renderer::applyHandler(), 
 * see what we can do there to support invoked calls
 * 
 *
 */
class Element extends \lithium\template\Helper {
	
	public function __invoke() {
		$args = func_get_args();
		return $this->invokeMethod('render', $args);
	}
	
	public function render($template, array $data = array(), array $options = array()) {
		return $this->_context->view()->render(array('element' => $template), $data, $options);
	}
}

?>