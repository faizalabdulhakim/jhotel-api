<?php

class Reservation_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getReservations($limit = 10, $offset = 0, $keyword = '')
	{
		$this->db->like('user_id', $keyword);
		$this->db->or_like('room_id', $keyword);
		$this->db->or_like('check_in_date', $keyword);
		$this->db->or_like('check_out_date', $keyword);
		$this->db->or_like('status', $keyword);
		$this->db->limit($limit, $offset);
		$this->db->order_by('created_at', 'asc');
		return $this->db->get('reservations')->result_array();
	}

	public function getReservationById($id)
	{
		$query = $this->db->get_where('reservations', ['id' => $id])->result_array();
		if (empty($query)) {
			return null;
		}
		return $query[0];
	}

	public function countReservations()
	{
		return $this->db->count_all('reservations');
	}

	public function createReservation($data)
	{
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

	public function getRevenue()
	{
		$this->db->select_sum('rooms.price', 'total_revenue');
		$this->db->where('status', 'PAID');
		$this->db->from('reservations');
		$this->db->join('rooms', 'reservations.room_id = rooms.id');
		$query = $this->db->get();

		$result = $query->row();
		return $result->total_revenue;
	}
}
