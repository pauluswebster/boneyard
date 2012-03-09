<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\util;

/**
 * The `Events` class extends upon Lihtium's native `Filters` implementation
 * to provide convenient access for applying callable event functions on
 * class methods.
 *
 * `Events` applies event functions to a classes filter chain for a given
 * method, giving the option of execution before or after the method's closure.
 * Additionally it allows these events to be pulled out of execution by
 * tracking each event with a unique key that can be passed back to `Events`,
 * and/or by specifying events as 'run once'.
 *
 * The following examples are fundamentally the same, but illustrate the
 * limitations imposed by `Events` methods over standard filters applied to a
 * class:
 *
 * {{{
 * $model = 'app\models\Posts';
 *
 * //Standard filter
 * Posts::applyFilter('save', function($self, $params, $chain){
 * 		//do something
 * 		Logger::write('debug', 'Someone saved a post');
 * 		//...
 * 		//return filter chain call
 * 		return $chain->next($self, $params, $chain);
 * });
 *
 * //Events filter
 * Events::apply($model, 'save', function($self, $params, $key){
 *		//do something
 * 		Logger::write('debug', 'Someone saved a post');
 * 		//...
 * 		//no return
 * });
 * }}}
 *
 * In this example the `Events` filter does not receive the call chain, and
 * therefore cannot control it's execution. Similarly, while it receives the
 * params passed to the filter (or result if executing after), the event
 * filter cannot alter the params (excepting referenecs of course) as no return
 * value is taken from the event.
 */
class Events extends \lithium\core\StaticObject {

	/**
	 * Events keys
	 *
	 * @var array
	 */
	protected static $_keys = array();

	/**
	 * Attach event to class method
	 *
	 * @param mixed $class class instnace or static name
	 * @param string $method class method
	 * @param mixed $event callable
	 * @param boolean $before observe before class method filter
	 * @param boolean $always false event is only run once or true
	 * @return string event key
	 */
	public static function add($class, $method, $event, $before = true, $always = true) {
		return static::_apply($class, $method, $event, $before, $always);
	}

	/**
	 * Attach event to class method (before class method filter)
	 *
	 * @param mixed $class class instnace or static name
	 * @param string $method class method
	 * @param mixed $event callable
	 * @param boolean $always false event is only run once or true
	 * @return string event key
	 */
	public static function before($class, $method, $event, $always = true) {
		return static::add($class, $method, $event, true, $always);
	}

	/**
	 * Attach event to class method (after class method filter)
	 *
	 * @param mixed $class class instnace or static name
	 * @param string $method class method
	 * @param mixed $event callable
	 * @param boolean $always false event is only run once or true
	 * @return string event key
	 */
	public static function after($class, $method, $event, $always = true) {
		return static::add($class, $method, $event, false, $always);
	}

	/**
	 * Check event identified by key is applied
	 *
	 * @param string $key
	 * @return boolean
	 */
	public static function applied($key) {
		if (array_search($key, static::$_keys)) {
			return true;
		}
		return false;
	}

	/**
	 * Remove event identified by key
	 *
	 * @param string $key
	 * @return boolean
	 */
	public static function remove($key) {
		if ($index = array_search($key, static::$_keys)) {
			unset(static::$_keys[$index]);
			return true;
		}
		return false;
	}

	/**
	 * Apply the event to class method filters
	 *
	 * @param mixed $class class instnace or static name
	 * @param string $method class method
	 * @param mixed $event callable
	 * @param boolean $before
	 * @param boolean $always
	 * @return string event key
	 */
	protected static function _apply($class, $method, $event, $before, $always) {
		static $_k = 0;
		$_k++;
		$where = $before ? 'before' : 'after';
		$key = md5("{$method}.{$where}." . $_k);
		static::$_keys[$_k] = $key;
		$ob = get_called_class();
		$filter = function($self, $params, $chain) use($event, $key, $before, $always, $ob){
			if ($before) {
				$ob::invokeMethod('_call', array($event, $self, $params, $key));
			}
			$result = $chain->next($self, $params, $chain);
			if (!$before) {
				$ob::invokeMethod('_call', array($event, $self, $result, $key));
			}
			if (!$always) {
				$ob::remove($key);
			}
			return $result;
		};
		if (is_object($class) || class_exists($class, false)) {
			call_user_func(array($class, 'applyFilter'), $method, $filter);
		} else {
			Filters::apply($class, $method, $filter);
		}
		return $key;
	}

	/**
	 * Call an event
	 *
	 * @param mixed $event callable
	 * @param mixed $params array params or mixed result
	 * @param string $key event key
	 * @return null
	 */
	protected static function _call($event, $class, $params, $key) {
		if ($index = array_search($key, static::$_keys)) {
			$event($class, $params, $key);
		}
	}
}

?>