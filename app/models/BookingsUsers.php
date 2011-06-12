<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\models;

class BookingsUsers extends \lithium\data\Model {
	
	public $belongsTo = array(
		'User' => array(
			'to' => 'app\models\Users',
		),
		'Booking' => array(
			'to' => 'app\models\Bookings'
		)
	);
	
	public static function User($record, $query = array()) {
		$relationship = static::relations('User');
		$model = $relationship->to();
		$conditions = array_flip($relationship->keys());	
		array_walk($conditions, function(&$field) use ($record){
			$field = $record->{$field};
		});
		$query = compact('conditions') + $query;
		return $model::first($query);
	}
	
}

?>