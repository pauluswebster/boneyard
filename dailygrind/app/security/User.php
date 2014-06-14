<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\security;

use app\models\JobLogs;
use sli_base\storage\Registry;
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

	public function job($query = false) {
		if ($user_id = $this->id) {
			$job = $this->retrieve('job.current');
			if (!$job && $query && $job = JobLogs::current($user_id)) {
				$this->store('job.current', $job->data());
			}
			return $job;
		}
	}

	public function active($record = null, $query = true) {
		if ($user_id = $this->id) {
			if (is_object($record)) {
				$this->store('job.current', $record->key());
				$active = $record;
			} else {
				$active = $this->retrieve('job.current');
				if ($active) {
					$active = JobLogs::active($active);
				} else if (!$active && $query && $active = JobLogs::current($user_id)) {
					$this->store('job.current', $active->key());
				}
			}
			return $active;
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