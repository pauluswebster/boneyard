<?php

namespace app\controllers;

use lithium\net\http\Service;
use app\models\Listings;

class DealersController extends \lithium\action\Controller {

	public function index() {

//		Listings::importData();
		
		die;
		
		$url = parse_url('http://www.motortraders.med.govt.nz');
		$path = '/motortraders-web/RegisterSearch.do';
		$s = new Service($url);
		
		
		
		$data = array(
//			'tradingNameOperator' => 'Starts With',
//			'tradingName' => 'Auto Court'
			
			'selectedTraderNumber' => 'M213755',
			'action' => 'selectRow'
//			p_access_no:D53B50C206A4784593093BA319778353
			
		);
		
		print($s->post($path, $data));
		
		
		die;
	}
}

?>