<?php

class Room_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getRooms($id = null)
	{
		if ($id === null) {
			return $this->db->get('rooms')->result_array();
		} else {
			return $this->db->get_where('rooms', ['id' => $id])->result_array();
		}
	}

	public function createRoom($data)
	{
		$this->db->insert('rooms', $data);
		return $this->db->insert_id();
	}

	public function updateRoom($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('rooms', $data);
		return $this->db->affected_rows();
	}

	public function deleteRoom($id)
	{
		$this->db->delete('rooms', ['id' => $id]);
		return $this->db->affected_rows();
	}
}
