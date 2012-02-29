<?php
namespace app\controllers;

use sli_util\storage\Registry;

class PagesController extends SiteController {

	public function view() {
		$args = func_get_args();
		$path = empty($args) || empty($args[0]) ? array('home') : $args;
		return $this->render(array('template' => join('/', $path)));
	}
	
	/**
	 * @todo 
	 * 1. Validation proper
	 * 2. template responses
	 * 3. Mailer & mail template
	 */
	public function contact () {
		$ref = isset($_SERVER['HTTP_REFERER']) && in_array(strpos($_SERVER['HTTP_REFERER'], "://{$_SERVER['HTTP_HOST']}"), array(4, 5));
		if (!empty($_POST) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest" && $ref) {
			
			$textFilter = array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_ENCODE_HIGH
			);
			
			$filters = array(
				'name' => $textFilter,
				'email' => FILTER_SANITIZE_EMAIL,
				'subject' => $textFilter,
				'message' => $textFilter
			);
			
			$data = filter_input_array(INPUT_POST, $filters);
			$errors = array();
			
			if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$errors[] = 'email';
			}
			$empty = function($field) use (&$errors, $data){
				$value = trim($data[$field]);
				if (empty($value)) {
					$errors[] = $field;
				}
			};
			array_map($empty, array('name', 'subject', 'message'));
			
			if (!empty($errors)) {
				$success = false;
			} else {
				$message = "
				
				Name: {$data['name']}
				Email: {$data['email']}
				Subject: {$data['subject']}
				
				Message:
				----------------------------------------
				
				{$data['message']}
				
				----------------------------------------
				IP: {$_SERVER['REMOTE_ADDR']}
				
				";
			
				$subject = 'Enquiry From Website';
				$to = Registry::get('env.contact.to');
				$success = mail($to, $subject, $message);
			}
			
			echo json_encode(compact('success', 'errors'));
			exit;
		
		}
		
		return $this->redirect('/');
	}
}

?>