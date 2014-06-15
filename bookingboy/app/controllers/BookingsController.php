<?php

namespace app\controllers;

use app\models\Users;
use app\models\Items;
use app\models\Bookings;
use lithium\util\Set;
use lithium\storage\Session;
use sli_users\security\CurrentUser;
use sli_util\storage\Registry;

class BookingsController extends \sli_scaffold\controllers\ScaffoldController {
	
	protected function _init() {
        parent::_init();
        $this->applyFilter('__invoke', function($self, $params, $chain){
        	$settings = $self->_settings;
        	$self->_timezone = $timezone = new \DateTimeZone($settings['timezone']);
			$self->_interval = $interval = new \DateInterval($settings['listingInterval']);
			$self->_int = $int = $settings['listingIntervalSeconds'];
			$self->set(compact('timezone', 'interval', 'int'));
			return $chain->next($self, $params, $chain);
        });        
    }

	public function index() {
		$day = 'today';
		if(!empty($this->request->query['today'])) {
			$day = $this->request->query['today'];
			Session::write('bookings.today', $day, array('name' => 'cookie'));	
		} elseif ($_day = Session::read('bookings.today', array('name' => 'cookie'))) {
			$day = $_day;
		}
		$date = new \DateTime('today', $this->_timezone);//actual today
		$today = new \DateTime($day, $this->_timezone);//day being viewed
		if($date == $today) {
			Session::delete('bookings.today', array('name' => 'cookie'));
		}
		$tomorrow = clone $today;
		$tomorrow->add(new \DateInterval('P1D'));
		
		$items = Items::all();
		$itemIds = Set::extract(array_values($items->data()), '/id');
		
		$start = clone $today;
		if (!empty($this->_settings['start'])) {
			$start->add(new \DateInterval($this->_settings['start']));
		}
		if (!empty($this->_settings['end'])) {
			$end = clone $today;
			$end->add(new \DateInterval($this->_settings['end']));
		} else {
			$end = clone $tomorrow;
		}
		
		$startTime = $start->getTimestamp();
		$endTime = $end->getTimestamp();
		$bookings = Bookings::all(array(
			'conditions' => array(
				'or' => array(
					'start' => array('between' => array($startTime, $endTime)),
					'end' => array('between' => array($startTime, $endTime))
				),
				'item_id' => $itemIds
			)
		));
		
		$bookings->each(function($booking) use (&$items) {
			if (!$items[$booking->item_id]->bookings) {
				$items[$booking->item_id]->bookings = array();
			}
			$items[$booking->item_id]->bookings[] = $booking;
		});
		$this->set(compact('date', 'today', 'tomorrow', 'start', 'end', 'startTime', 'endTime', 'items'));
	}
	
	public function add() {
		$this->_render['template'] = 'form';
		parent::add();
		$record = array('creator_id' => $this->_user->id, 'users' => array($this->_user->id));
		$item = null;
		if (isset($this->request->query['item'])) {
			$record['item_id'] = (int) $this->request->query['item'];
		}
		if (isset($this->request->query['start'])) {
			$record['start'] = (int) $this->request->query['start'];
		}
		$this->_render['data']['record'] = Bookings::create($record);
		$items = Items::find('list');
		$userList = Users::find('list');
		$this->set(compact('userList', 'items'));
	}
	
	public function edit() {
		if (!empty($this->request->data['action'])) {
			if ($this->request->data['action'] == 'delete') {
				return $this->delete();
			}
		}
		$this->_render['template'] = 'form';
		parent::edit();
		$items = Items::find('list');
		$userList = Users::find('list');
		$this->set(compact('userList', 'items'));
	}
}

?>