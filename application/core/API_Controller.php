<?php
defined('BASEPATH') or exit('No direct script access allowed');

class API_Controller extends CI_Controller
{
	protected $isAuthorized = false;

	public function __construct()
	{
		parent::__construct();
		$this->checkAuthorization();
	}

	// Centralized authorization check
	protected function checkAuthorization()
	{
		$headers = $this->input->request_headers();
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
			if ($decodedToken['status']) {
				$this->isAuthorized = true;
			} else {
				$this->response([
					'status' => false,
					'message' => 'Invalid token'
				], 401);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => 'Token required'
			], 401);
		}
	}

	// Helper function to output JSON response
	protected function response($data, $http_code = 200)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($http_code)
			->set_output(json_encode($data));
	}
}
