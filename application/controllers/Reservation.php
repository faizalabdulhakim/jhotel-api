<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/core/API_Controller.php';

class Reservation extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Reservation_model');
	}

	public function index($id = null)
	{
		if (!$this->isAuthorized) return;

		$id = $this->input->get('id');

		if ($id === null) {
			$reservations = $this->Reservation_model->getReservations();
		} else {
			$reservations = $this->Reservation_model->getReservations();
			if (!$reservations) {
				$this->response([
					'status' => false,
					'message' => 'Reservation not found'
				], 404);
				return;
			}
		}

		$this->response([
			'status' => true,
			'data' => $reservations

		], 200);
	}

	public function create()
	{
		if (!$this->isAuthorized) return;
		$input = json_decode(file_get_contents("php://input"), true);

		try {
			$reservation_id = $this->Reservation_model->createReservation($input);

			if ($reservation_id) {
				$this->response([
					'status' => true,
					'message' => 'Reservation created successfully',
					'data' => ['reservation_id' => $reservation_id]
				], 201);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to create reservation'
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

		$reservation = $this->Reservation_model->getReservations($id);

		if (empty($reservation)) {
			$this->response([
				'status	' => false,
				'message' => 'Reservation not found'
			], 404);
			return;
		}

		try {
			$updated = $this->Reservation_model->updateReservation($id, $input);

			if ($updated) {
				$this->response([
					'status' => true,
					'message' => 'Reservation updated successfully'
				], 200);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to update reservation'
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

		$reservation = $this->Reservation_model->getReservations($id);
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
				$this->response([
					'status' => true,
					'message' => 'Reservation deleted successfully'
				], 204);
			} else {
				$this->response([
					'status' => false,
					'message' => 'Failed to delete reservation'
				], 500);
			}
		} catch (Exception $e) {
			$this->response([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function revenue()
	{
		if (!$this->isAuthorized) return;

		$revenue = $this->Reservation_model->getRevenue();

		$this->response([
			'status' => true,
			'data' => $revenue

		], 200);
	}
}
