<?php

class Reservation_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getReservations($id = null)
	{
		if ($id === null) {
			return $this->db->get('reservations')->result_array();
		} else {
			return $this->db->get_where('reservations', ['id' => $id])->result_array();
		}
	}

	public function createReservation($data)
	{
		// dump and die data
		// var_dump($data);

		$this->db->insert('reservations', $data);
		return $this->db->insert_id();
	}

	public function updateReservation($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('reservations', $data);
		return $this->db->affected_rows();
	}

	public function deleteReservation($id)
	{
		$this->db->delete('reservations', ['id' => $id]);
		return $this->db->affected_rows();
	}
}
