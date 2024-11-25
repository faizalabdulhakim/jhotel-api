<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/core/API_Controller.php';

class Reservation extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Reservation_model');
		$this->load->model('User_model');
		$this->load->model('Room_model');
	}

	public function index()
	{
		if (!$this->isAuthorized) return;

		$limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
		$offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
		$keyword = $this->input->get('keyword') ? $this->input->get('keyword') : '';

		$pageNumber = ceil($offset / $limit + 1);

		$reservations = $this->Reservation_model->getReservations($limit, $offset, $keyword);

		return $this->response([
			'status' => true,
			'pageNumber' => $pageNumber,
			'pageSize' => count($reservations),
			'totalRecordCount' => $this->Reservation_model->countReservations(),
			'data' => $reservations
		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		
		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('user_id', 'User ID', 'required|numeric');
		$this->form_validation->set_rules('room_id', 'Room ID', 'required|numeric');
		$this->form_validation->set_rules('check_in_date', 'Check In', 'required|regex_match[/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/]');
		$this->form_validation->set_rules('check_out_date', 'Check Out', 'required|regex_match[/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[PENDING,PAID,FINISH]');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		$user = $this->User_model->getUserById($input['user_id']);
		$room = $this->Room_model->getRoomById($input['room_id']);

		if(!$user){
			return $this->response([
				'status' => false,
				'message' => 'User with ' . $input['user_id'] . ' doesn\'t exist'
			], 400);
		}

		if(!$room){
			return $this->response([
				'status' => false,
				'message' => 'Room with ' . $input['room_id'] . ' doesn\'t exist'
			], 400);
		}

		try {
			$this->Room_model->updateRoom($input['room_id'], ['availability' => 1]);

			$reservation_id = $this->Reservation_model->createReservation($input);

			if ($reservation_id) {
				return $this->response([
					'status' => true,
					'message' => 'Reservation created successfully',
					'data' => ['reservation_id' => $reservation_id]
				], 201);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to create reservation'
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

		$input = json_decode(file_get_contents("php://input"), true);

		$this->form_validation->set_data($input);
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[PENDING,PAID,FINISH]');

		if (!$this->form_validation->run()) {
			return $this->response([
				'status' => false,
				'message' => strip_tags(validation_errors())
			], 400);
		}

		$reservation = $this->Reservation_model->getReservationById($id);

		if (empty($reservation)) {
			return $this->response([
				'status	' => false,
				'message' => 'Reservation not found'
			], 404);
		}

		$user = $this->User_model->getUserById($reservation['user_id']);
		$room = $this->Room_model->getRoomById($reservation['room_id']);

		if(!$user){
			return $this->response([
				'status' => false,
				'message' => 'User with ' . $reservation['user_id'] . ' doesn\'t exist'
			], 400);
		}

		if(!$room){
			return $this->response([
				'status' => false,
				'message' => 'Room with ' . $reservation['room_id'] . ' doesn\'t exist'
			], 400);
		}

		try {
			// if finish the room is vacant
			if($input['status'] === 'FINISH'){
				$this->Room_model->updateRoom($reservation['room_id'], ['availability' => 0]);
			}

			$updated = $this->Reservation_model->updateReservation($id, $input);

			if ($updated === 0) {
				return $this->response([
					'status' => true,
					'message' => 'Room updated successfully'
				], 200);
			}

			if ($updated) {
				return $this->response([
					'status' => true,
					'message' => 'Reservation updated successfully'
				], 200);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to update reservation'
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

		$reservation = $this->Reservation_model->getReservationById($id);
		if (!$reservation) {
			$this->response([
				'status' => false,
				'message' => 'Reservation not found'
			], 404);
			return;
		}

		try {
			$deleted = $this->Reservation_model->deleteReservation($id);

			if ($deleted) {
				return $this->response([
					'status' => true,
					'message' => 'Reservation deleted successfully'
				], 204);
			} else {
				return $this->response([
					'status' => false,
					'message' => 'Failed to delete Reservation'
				], 500);
			}
		} catch (Exception $e) {
			return $this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function revenue()
	{
		if (!$this->isAuthorized) return;

		$revenue = $this->Reservation_model->getRevenue();

		return $this->response([
			'status' => true,
			'data' => $revenue

		], 200);
	}
}
