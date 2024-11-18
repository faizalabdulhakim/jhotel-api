<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/core/API_Controller.php';

class Room extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Room_model');
	}

	public function index($id = null)
	{
		if (!$this->isAuthorized) return;

		$id = $this->input->get('id');

		if ($id === null) {
			$rooms = $this->Room_model->getRooms();
		} else {
			$rooms = $this->Room_model->getRooms();
			if (!$rooms) {
				$this->response([
					'status' => false,
					'message' => 'Room not found'
				], 404);
				return;
			}
		}

		$this->response([
			'status' => true,
			'data' => $rooms

		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		$input = json_decode(file_get_contents("php://input"), true);

		try {
			$room_id = $this->Room_model->createRoom($input);

			if ($room_id) {
				$this->response([
					'status' => true,
					'message' => 'Room created successfully',
					'data' => ['room_id' => $room_id]
				], 201);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to create room'
				], 500);
			}
		} catch (Exception $e) {
			$this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function update($id)
	{
		if (!$this->isAuthorized) return;

		$input = json_decode(file_get_contents("php://input"), true);

		$room = $this->Room_model->getRooms($id);

		if (empty($room)) {
			$this->response([
				'status	' => false,
				'message' => 'Room not found'
			], 404);
			return;
		}

		try {
			$updated = $this->Room_model->updateRoom($id, $input);

			if ($updated) {
				$this->response([
					'status' => true,
					'message' => 'Room updated successfully'
				], 200);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to update room'
				], 500);
			}
		} catch (Exception $e) {
			$this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function delete($id)
	{
		if (!$this->isAuthorized) return;

		$room = $this->Room_model->getRooms($id);
		if (!$room) {
			$this->response([
				'status' => false,
				'message' => 'Room not found'
			], 404);
			return;
		}

		try {
			$deleted = $this->Room_model->deleteRoom($id);

			if ($deleted) {
				$this->response([
					'status' => true,
					'message' => 'Room deleted successfully'
				], 204);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to delete room'
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
