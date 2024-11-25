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

	public function index()
	{
		if (!$this->isAuthorized) return;

		$limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
		$offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
		$keyword = $this->input->get('keyword') ? $this->input->get('keyword') : '';

		$pageNumber = ceil($offset / $limit + 1);

		$users = $this->User_model->getUsers($limit, $offset, $keyword);
		return $this->response([
			'status' => true,
			'pageNumber' => $pageNumber,
			'pageSize' => count($users),
			'totalRecordCount' => $this->User_model->countUsers(),
			'data' => $users
		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]', [
			'is_unique' => 'The email is already taken'
		]);
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('password_confirmation', "Password Confirmation", 'required');
		$this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,guest]');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		if ($input['password'] !== $input['password_confirmation']) {
			return $this->response([
				'status' => false,
				'message' => 'Password confirmation does not match'
			], 400);
		}

		unset($input['password_confirmation']);
		$input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

		try {
			$user_id = $this->User_model->createUser($input);

			if ($user_id) {
				return $this->response([
					'status' => true,
					'message' => 'User created successfully',
					'data' => ['user_id' => $user_id]
				], 201);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to create user'
				], 500);
			}
		} catch (Exception $e) {
			return $this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	// Update an existing user
	public function update($id)
	{
		if (!$this->isAuthorized) return;

		$user = $this->User_model->getUserById($id);
		if (empty($user)) {
			return $this->response([
				'status' => false,
				'message' => 'User not found'
			], 404);
		}

		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('name', 'Name', 'required');

		if (isset($input['email']) && $input['email'] !== $user['email']) {
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]', [
				'is_unique' => 'The email is already taken'
			]);
		} else {
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		}

		$this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,guest]');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		try {
			$updated = $this->User_model->updateUser($id, $input);

			if ($updated === 0) {
				return $this->response([
					'status' => true,
					'message' => 'User updated successfully'
				], 200);
			}

			if ($updated) {
				return $this->response([
					'status' => true,
					'message' => 'User updated successfully'
				], 200);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to update user'
				], 500);
			}
		} catch (Exception $e) {
			return $this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function delete($id)
	{
		if (!$this->isAuthorized) return;

		$user = $this->User_model->getUserById($id);
		if (!$user) {
			$this->response([
				'status' => false,
				'message' => 'User not found'
			], 404);
			return;
		}

		try {
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
		} catch (Exception $e) {
			$this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}
}
