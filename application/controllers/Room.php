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

	public function index()
	{
		if (!$this->isAuthorized) return;

		$limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
		$offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
		$keyword = $this->input->get('keyword') ? $this->input->get('keyword') : '';

		$pageNumber = ceil($offset / $limit + 1);

		$rooms = $this->Room_model->getRooms($limit, $offset, $keyword);

		return $this->response([
			'status' => true,
			'pageNumber' => $pageNumber,
			'pageSize' => count($rooms),
			'totalRecordCount' => $this->Room_model->countRooms(),
			'data' => $rooms
		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('price', 'Price', 'required|numeric');
		$this->form_validation->set_rules('description', 'Description', 'required|trim');
		$this->form_validation->set_rules('availability', 'Availability', 'required|numeric|in_list[0,1]');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		try {
			$room_id = $this->Room_model->createRoom($input);

			if ($room_id) {
				return $this->response([
					'status' => true,
					'message' => 'Room created successfully',
					'data' => ['room_id' => $room_id]
				], 201);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to create room'
				], 500);
			}
		} catch (Exception $e) {
			return $this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function update($id)
	{
		if (!$this->isAuthorized) return;


		$room = $this->Room_model->getRoomById($id);
		if (empty($room)) {
			$this->response([
				'status	' => false,
				'message' => 'Room not found'
			], 404);
			return;
		}

		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('price', 'Price', 'required|numeric');
		$this->form_validation->set_rules('description', 'Description', 'required|trim');
		$this->form_validation->set_rules('availability', 'Availability', 'required|numeric');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		try {
			$updated = $this->Room_model->updateRoom($id, $input);

			if ($updated === 0) {
				return $this->response([
					'status' => true,
					'message' => 'Room updated successfully'
				], 200);
			}

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

		$room = $this->Room_model->getRoomById($id);
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
