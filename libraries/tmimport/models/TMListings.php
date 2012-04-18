<?php

namespace tmimport\models;

use lithium\storage\Cache;
use lithium\util\Set;
use lithium\util\Collection;

use app\models\Listings;

class TMListings extends \lithium\data\Model {
	
	protected $_meta = array(
		'connection' => false
	);
	
	public static function price($record) {
		if (empty($record->price_text)) {
			return  '$' . number_format($record->price, 2);
		}
		return $record->price_text;
	}
	
	public static function photo($record) {
		return $record->id . '/' . $record->photo . '.jpg';
	}
	
	public static function attributes($record, $limit = null) {
		$keys = array(
			'kilometres',
			'fuel_type',
			'engine_size',
			'cylinders',
			'transmission',
			'4wd',
			'body_style',
			'doors',
			'exterior_color',
			'number_of_owners',
			'import_history',
			'registration_expires',
			'wof_expires',
			'stereo_description',
			'Features'
		);
		$attributes = array_fill_keys($keys, null);
		foreach ($attributes as $k => &$v) {
			if (isset($record->attributes[$k])) {
				$v = $record->attributes[$k];
			}
		}
		$length = $limit ?: count($attributes);
		return array_slice(array_filter($attributes), 0, $length, true);
	}
	
	public static function listImported($limit = null, $order = '/listed desc', $randomize = true) {
		if (is_array($limit)) {
			extract($limit);
		}
		$source = Cache::read('default', 'imported.listing') ?: array();
		$map = function($listing) use (&$source){
			$id = basename($listing);
			$source[$id] = $listing;
		};
		if (empty($source)) {
			$path = LITHIUM_APP_PATH . '/resources/Used';
			$listings = glob($path . '/*', GLOB_ONLYDIR);
			array_map($map, $listings);
			Cache::write('default', 'imported.listing', $source);
		}
		if ($randomize) {
			$_source = $source;
			shuffle($_source);
			$source = array();
			array_map($map, $_source);
		}
		reset($source);
		$list = new Collection();
		$count = count($source);
		$limit = (!$limit || $limit > $count) ? $count : $limit;
		while ($limit--) {
			$id = key($source);
			$list[$id] = static::getImported($id);
			next($source);
		}
		if ($order) {
			$sort = explode(' ', $order, 2);
			list($f, $s) = (array) $sort + array('/listed', 'desc');
			$sorted = Set::sort(array_values($list->to('array')), $f, $s);
			$index = array_fill_keys(Set::extract($sorted, '/id'), null);
			foreach ($index as $i => &$v) {
				$v = $list[$i];
			}
			$list = new Collection(array('data' => $index));
		}
		return $list;	
	}
	
	public static function getImported($id) {
		
		$dater = function($string){
			return (int) (str_replace(array('/Date(', ')', '/'), '', $string) / 1000);
		};
		
		if (!($data = Cache::read('default', 'imported.listing.' . $id))) {
			$raw = static::_loadImported($id);
			$photos = array_slice(Set::extract($raw['Photos'], '/Key'), 0, 10);
//			var_dump($raw);
			$data = array(
				'id' => $raw['ListingId'],
				'title' => $raw['Title'],
				'price' => $raw['StartPrice'],
				'price_text' => $raw['PriceDisplay'],
				'description' => $raw['Body'],
				'photo' => current($photos),
				'photos' => $photos,
				'attributes' => Set::combine($raw['Attributes'], '/Name', '/Value'),
				'location' => "{$raw['Suburb']}, {$raw['Region']}",
				'listed' => rand(strtotime('-2 weeks'), strtotime('-1 minute')),
				'expires' => rand(strtotime('tomorrow'), strtotime('+1 month')),
			);
//			var_dump($data);
		}
//		Cache::write('default', 'imported.listing.' . $id, $data);
		return static::create($data);
	}
	
	protected static function _loadImported($id) {
		$path = LITHIUM_APP_PATH . '/resources/Used/' . $id . '/Listing.json';
		return json_decode(file_get_contents($path), true);
	}
	
	public static function importRecords() {
		
		$categories = \app\models\Categories::find('list', array('conditions' => array('category_id' => 1)));
		
		$listings = static::listImported(array('limit' => false, 'order' => false, 'randomize' => false));
		
		foreach ($listings as $_l) {
			$listing = Listings::create(array(
				'user_id' => 1,
				'category_id' => array_rand($categories),
				'title' => $_l->title,
				'listed' => $_l->listed,
				'expires' => $_l->expires,
				'description' => $_l->description
			));
			$listing->save();
		}
		
		
		var_dump($listings);
		
	}
	
	public static function importData($query = '') {
		$path = LITHIUM_APP_PATH . '/resources/Used';
		@mkdir($path, 0777, true);
		//step 1
		if ($query) {
			$data = file_get_contents('http://api.trademe.co.nz/v1/Search/Motors/Used.json?search_string=' . $query);
			file_put_contents($path . '.json', $data);
		}
		
	
		//step 2
		$data = json_decode(file_get_contents($path . '.json'), true);
		foreach ($data['List'] as $li) {
			$lipath = $path . '/' . $li['ListingId'];
			$plipath = $path . '/' . $li['ListingId'] . '/Photos';
			if (!@file_get_contents($lipath . '/Listing.json')) {
				//step 2
				@mkdir($plipath, 0777, true);
				if ($listing = @file_get_contents('http://api.trademe.co.nz/v1/Listings/' . $li['ListingId'] . '.json')) {
					file_put_contents($lipath . '/Listing.json', $listing);
				} else {
					@unlink($lipath . '/Listing.json');
					@rmdir($plipath);
					@rmdir($lipath);
					continue;	
				}
			}
			//step 3
			$listing = json_decode(file_get_contents($lipath . '/Listing.json'), true);
			if ($listing == '') {
				continue;
			}
			foreach ($listing['Photos'] as $i => $photo) {
				if ($i == 10) {
					break;
				}
				$pfile = $plipath . '/' . $photo['Key'] . '.jpg';
				if (!file_exists($pfile)) {
					if ($pdata = file_get_contents($photo['Value']['FullSize'])) {
						file_put_contents($pfile, $pdata);
					}
				}
			}
		}
	}
	
}

?>