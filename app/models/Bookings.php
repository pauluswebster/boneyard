<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

use slicedup_core\configuration\Registry;
use app\models\BookingsUsers;

class Bookings extends \lithium\data\Model {
	
	public $hasMany = array(
		'Users' => array(
			'to' => 'app\models\BookingsUsers',
			'keys' => array('booking_id')
		)
	);
	
	public $belongsTo = array(
		'Item' => array(
			'to' => 'app\models\Items',
			'keys' => array('item_id')
		)
	);
	
	public static function __init(){
		parent::__init();
		
		$settings = Registry::get('bookings');
		
		static::applyFilter('save', function($self, $params, $chain) use ($settings){
			$data = $params['data'];
			$timezone = new \DateTimeZone($settings['timezone']);
			array_map(function($field) use (&$data, $timezone){
				if (!empty($data[$field]) && !is_numeric($data[$field])) {
					$dateTime = new \DateTime($data[$field], $timezone);
					$data[$field] = $dateTime->getTimestamp();
				}
			}, array('start', 'end'));
			$params['data'] = $data;
			if ($save = $chain->next($self, $params, $chain)) {
				if (!empty($data['users']) && $users = array_filter($data['users'])) {
					$record = $params['entity'];
					$currentUsers = BookingsUsers::all(array(
						'conditions' => array(
							'booking_id' => $record->id
						)
					));
					if (!empty($currentUsers)) {
						foreach ($currentUsers as $currentUser) {
							if ($key = array_search($currentUser->key(), $data['users'])) {
								unset($data['users'][$key]);
							} else {
								$currentUser->delete();
							}
						}
						if (!empty($data['users'])) {
							foreach ($data['users'] as $user) {
								$association = BookingsUsers::create(array(
									'booking_id' => $record->id,
									'user_id' => $user
								));
								$association->save();	
							}
						}
					}
					if (empty($record->title)) {
						$record->title = join(' & ', Users::find('list', array(
							'conditions' => array(
								'id' => $users
							)
						)));
						$record->save();
					}
				}
			}
			return $save;
		});
		
		static::applyFilter('create', function($self, $params, &$chain) use ($settings){
			if(empty($params['data'])) {
				return $chain->next($self, $params, $chain);
			}
			$data = $params['data'];
			$timezone = new \DateTimeZone($settings['timezone']);
			$format = function($field) use (&$data, $timezone, $settings){
				$dateTime = new \DateTime(null, $timezone);
				$dateTime->setTimestamp($data[$field]);
				$data["_{$field}"] = $dateTime;
				$data[$field] = $dateTime->format($settings['datePickerFormat']);
			};
			if (!empty($data['start']) && is_numeric($data['start'])) {
				$format('start');
				if (empty($data['end'])) {
					$interval = new \DateInterval($settings['bookingInterval']);
					$data['end']  = $data['_start']->add($interval)->getTimeStamp();
					$data['_start']->sub($interval);
				}
			}
			if (!empty($data['end']) && is_int($data['end'])) {
				$format('end');
			}
			$params['data'] = $data;
			return $chain->next($self, $params, $chain);
		});
		
		static::applyFilter('find', function($self, $params, &$chain) use ($settings){
			$result = $chain->next($self, $params, $chain);
			if ($result instanceOf \lithium\data\Entity) {
				$timezone = new \DateTimeZone($settings['timezone']);
				array_map(function($field) use (&$result, $timezone, $settings){
					if (!empty($result->{$field}) && is_numeric($result->{$field})) {
						$dateTime = new \DateTime(null, $timezone);
						$dateTime->setTimestamp($result->$field);
						$result->{"_{$field}"} = $dateTime;
						$result->$field = $dateTime->format($settings['datePickerFormat']);
					}
				}, array('start', 'end'));
			}
			return $result;
		});
	}
	
	public static function Users($record, $query = array()) {
		if (!$record->exists()) {
			return static::connection()->invokeMethod('_instance', array('set'));
		}
		$relationship = static::relations('Users');
		$model = $relationship->to;
		$conditions = $relationship->keys;		
		array_walk($conditions, function(&$field) use ($record){
			$field = $record->{$field};
		});
		$query = compact('conditions') + $query;
		return $model::all($query);
	}
	
	public static function Item($record, $query = array()) {
		$relationship = static::relations('Item');
		$model = $relationship->to;
		$conditions = array_flip($relationship->keys);	
		array_walk($conditions, function(&$field) use ($record){
			$field = $record->{$field};
		});
		$query = compact('conditions') + $query;
		return $model::first($query);
	}
}

?>