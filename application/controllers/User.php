<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/core/API_Controller.php';

class User extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index($id = null)
	{
		if (!$this->isAuthorized) return;

		$id = $this->input->get('id');

		if ($id === null) {
			$users = $this->User_model->getUsers();
		} else {
			$users = $this->User_model->getUsers($id);
			if (!$users) {
				$this->response([
					'status' => false,
					'message' => 'User not found'
				], 404);
				return;
			}
		}

		$this->response([
			'status' => true,
			'data' => $users

		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		$input = json_decode(file_get_contents("php://input"), true);

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

	// Update an existing user
	public function update($id)
	{
		if (!$this->isAuthorized) return;

		$input = json_decode(file_get_contents("php://input"), true);

		$user = $this->User_model->getUsers($id);
		if (empty($user)) {
			$this->response([
				'status' => false,
				'message' => 'User not found'
			], 404);
			return;
		}

		$updated = $this->User_model->updateUser($id, $input);

		if ($updated) {
			$this->response([
				'status' => true,
				'message' => 'User updated successfully'
			]);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Failed to update user'
			], 500);
		}
	}

	public function delete($id)
	{
		if (!$this->isAuthorized) return;

		$user = $this->User_model->getUsers($id);
		if (!$user) {
			$this->response([
				'status' => false,
				'message' => 'User not found'
			], 404);
			return;
		}

		$deleted = $this->User_model->deleteUser($id);

		if ($deleted) {
			$this->response([
				'status' => true,
				'message' => 'User deleted successfully'
			], 204);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Failed to delete user'
			], 500);
		}
	}
}
