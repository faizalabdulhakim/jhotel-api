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

		// hash the password and validate name, email, role = guest, and password
		$input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
		$input['role'] = 'guest';

		if (empty($input['name']) || empty($input['email']) || empty($input['role']) || empty($input['password'])) {
			$this->response([
				'status' => false,
				'message' => 'Invalid input'
			], 400);
			return;
		}

		// Insert user
		$user_id = $this->User_model->createUser($input);

		if ($user_id) {
			$this->response([
				'status' => true,
				'message' => 'User created successfully',
				'data' => ['user_id' => $user_id]
			], 201);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Failed to create user'
			], 500);
		}
	}

	public function login()
	{
		$input = json_decode(file_get_contents("php://input"), true);

		$email = $input['email'];
		$password = $input['password'];

		$user = $this->User_model->getUserByEmail($email);

		if ($user && password_verify($password, $user->password)) {
			$token_data['userEmail'] = $user->email;
			$token_data['userRole'] = $user->role;
			$tokenData = $this->authorization_token->generateToken($token_data);

			$this->response([
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
			$this->response([
				'status' => true,
				'message' => 'Token is valid'
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Invalid token'
			], 401);
		}
	}
}
