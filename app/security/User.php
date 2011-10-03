<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\security;

use app\models\JobLogs;
use sli_util\storage\Registry;
use app\util\TimeZones;
use app\util\CurrencyConverter;

class User extends \sli_users\security\User {

	public function _init() {
		extract($this->_config);
		$config = $class::config($configName);
		$class =& $this;
		$model = $config['model']['class'];
		$model::applyFilter('save', function($self, $params, $chain) use ($class){
			$save = $chain->next($self, $params, $chain);
			if (isset($class)) {
				if($save && $params['entity']->id ) {
					$record = $self::find('first', array(
						'conditions' => array(
							'id' => $params['entity']->id
						)
					));
					$data = $record->data();
					$class->set($data);
				}
			}
			return $save;
		});
		parent::_init();
	}

	public function job() {
		if ($user_id = $this->id) {
			return $this->retrieve('job.current');
		}
	}

	public function timezone(){
		if (!($timezone = $this->timezone)) {
			$timezone = Registry::get('app.timezone.default');
		}
		return $timezone;
	}

	public function currency(){
		if (!($currency = $this->currency)) {
			$currency = Registry::get('app.currency.default');
		}
		return $currency;
	}

	public function timezones() {
		$timezones = array($this->timezone());
		if($settings = $this->settings) {
			if (!empty($settings['timezones'])) {
				$timezones = $settings['timezones'];
			}
		}
		$timezones = array_intersect_key(TimeZones::get(false), $timezones);
		return array_combine($timezones, $timezones);
	}

	public function currencies($all = true) {
		$currencies = array($this->currency());
		if($settings = $this->settings) {
			if (!empty($settings['currencies'])) {
				$currencies = $settings['currencies'];
			}
		}
		return array_intersect_key(CurrencyConverter::currencies(), array_flip($currencies));
	}
}

?>