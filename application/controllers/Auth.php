<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function __construct($config = "rest")
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function register()
	{
		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]', [
			'is_unique' => 'The email is already taken'
		]);
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required');


		if (!$this->form_validation->run()) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => 'Validation error',
					'errors' => $this->form_validation->error_array()
				]));
		}

		if ($input['password'] !== $input['password_confirmation']) {
			return $this->response([
				'status' => false,
				'errors' => [
					'Password confirmation does not match',
				]
			], 400);
		}

		unset($input['password_confirmation']);
		$input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
		$input['role'] = 'guest';

		try {

			$user_id = $this->User_model->createUser($input);


			return $this->output
				->set_content_type('application/json')
				->set_status_header(201)
				->set_output(json_encode([
					'status' => true,
					'message' => 'User created successfully',
					'data' => ['user_id' => $user_id]
				]));
		} catch (Exception $e) {

			return $this->output
				->set_content_type('application/json')
				->set_status_header(500)
				->set_output(json_encode([
					'status' => false,
					'message' => 'Error creating user: ' . $e->getMessage(),
					'code' => $e->getCode()
				]));
		}
	}

	public function login()
	{
		$input = json_decode(file_get_contents("php://input"), true);

		$email = $input['email'];
		$password = $input['password'];

		$user = $this->User_model->getUserByEmail($email);

		if ($user && password_verify($password, $user->password)) {

			if ($user->role !== 'admin') {
				return $this->response([
					'status' => false,
					'message' => 'User must be admin!'
				], 401);
			}

			$token_data['userEmail'] = $user->email;
			$token_data['userRole'] = $user->role;
			$tokenData = $this->authorization_token->generateToken($token_data);

			return $this->response([
				'status' => true,
				'message' => 'User logged in successfully',
				'token' => $tokenData
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Invalid email or password'
			], 401);
		}
	}

	private function response($data, $http_code = 200)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($http_code)
			->set_output(json_encode($data));
	}

	public function validateToken()
	{
		$decodedToken = $this->authorization_token->validateToken();
		if ($decodedToken['status']) {
			return $this->response([
				'valid' => true,
				'message' => 'Token is valid'
			], 200);
		} else {
			return $this->response([
				'valid' => false,
				'message' => 'Invalid token'
			], 401);
		}
	}
}
