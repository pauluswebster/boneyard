<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace sli_base\data\model\behavior;

/**
 * The `Timestamped` class is a model behavior that auto populates record
 * fields with formated timestamps on save operations when records are created
 * & updated.
 */
class Timestamped extends \sli_base\data\model\Behavior {

	/**
	 * Config
	 *
	 * @var array
	 */
	protected static $_settings = array(
		'create' => array(
			'field' => 'created',
			'format' => null,
		),
		'update' => array(
			'field' => 'updated',
			'format' => null,
		),
		'format' => 'Y-m-d H:i:s',
		'check' => false
	);

	protected static function _apply($class, $settings) {
		$settings = parent::_apply($class, $settings);
		if (!empty($settings['check'])) {
			$fields[] = array();
			foreach (array('create', 'update') as $action) {
				if (empty($settings[$action])) {
					continue;
				}
				$field = $settings[$action];
				if (is_array($field)) {
					$field = $field['field'];
				}
				if (!static::_checkSchema($class, array($field))) {
					$settings[$action] = false;
				}
			}
		}
		return $settings;
	}

	/**
	 * Before save call back to create/update the timestamps
	 *
	 * @see lithium\data\Model::save()
	 * @param array $settings
	 * @param string $model Model class name
	 * @param array $params
	 * @return array $params
	 */
	public static function saveBeforeFilter($model, $params, $settings) {
		extract($settings);
		$time = time();
		$data =& $params['data'];
		$entity =& $params['entity'];
		if ($create && !$entity->exists()) {
			if (!is_array($create)) {
					$create = array('field' => $create, 'format' => null);
			}
			$data[$create['field']] = date(($create['format']?:$format), $time);
		}
		if ($update) {
			if (!is_array($update)) {
				$update = array('field' => $update, 'format' => null);
			}
			$data[$update['field']] = date(($update['format']?:$format), $time);
		}
		return $params;
	}
}

?>